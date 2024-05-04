<?php

class Habit extends Model
{
    public static function allByUser(int $userId): array
    {
        $today = date('Y-m-d');
        return self::query(
            'SELECT h.*, (SELECT COUNT(*) FROM habit_logs l WHERE l.habit_id = h.id AND l.log_date = ?) AS checked_today
             FROM habits h WHERE h.user_id = ? ORDER BY h.id DESC',
            [$today, $userId]
        )->fetchAll();
    }

    public static function create(int $userId, string $title): void
    {
        self::query('INSERT INTO habits (user_id, title) VALUES (?, ?)', [$userId, $title]);
    }

    public static function checkin(int $userId, int $habitId): void
    {
        $habit = self::query('SELECT id FROM habits WHERE id = ? AND user_id = ?', [$habitId, $userId])->fetch();
        if ($habit) {
            self::query('INSERT OR IGNORE INTO habit_logs (habit_id, log_date) VALUES (?, ?)', [$habitId, date('Y-m-d')]);
        }
    }

    public static function delete(int $userId, int $habitId): void
    {
        self::query(
            'DELETE FROM habit_logs WHERE habit_id IN (SELECT id FROM habits WHERE id = ? AND user_id = ?)',
            [$habitId, $userId]
        );
        self::query('DELETE FROM habits WHERE id = ? AND user_id = ?', [$habitId, $userId]);
    }

    public static function streak(int $habitId): int
    {
        $dates = self::query('SELECT log_date FROM habit_logs WHERE habit_id = ? ORDER BY log_date DESC', [$habitId])
            ->fetchAll(PDO::FETCH_COLUMN);

        $streak = 0;
        $day = new DateTime('today');
        while (in_array($day->format('Y-m-d'), $dates)) {
            $streak++;
            $day->modify('-1 day');
        }
        return $streak;
    }

    public static function completionRate(int $habitId, int $days = 30): int
    {
        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $count = self::query('SELECT COUNT(*) FROM habit_logs WHERE habit_id = ? AND log_date >= ?', [$habitId, $from])
            ->fetchColumn();
        return (int)round($count / $days * 100);
    }
}
