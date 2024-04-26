<?php

class Task extends Model
{
    public static function allByUser(int $userId): array
    {
        return self::query('SELECT * FROM tasks WHERE user_id = ? ORDER BY done, id DESC', [$userId])->fetchAll();
    }

    public static function create(int $userId, string $title): void
    {
        self::query('INSERT INTO tasks (user_id, title) VALUES (?, ?)', [$userId, $title]);
    }

    public static function markDone(int $userId, int $id): void
    {
        self::query('UPDATE tasks SET done = 1 WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function delete(int $userId, int $id): void
    {
        self::query('DELETE FROM tasks WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function stats(int $userId): array
    {
        $row = self::query('SELECT COUNT(*) AS total, SUM(done) AS done FROM tasks WHERE user_id = ?', [$userId])->fetch();
        $total = (int)$row['total'];
        $done = (int)$row['done'];
        return [
            'total' => $total,
            'done' => $done,
            'percent' => $total > 0 ? round($done / $total * 100) : 0,
        ];
    }
}
