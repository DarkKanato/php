<?php

declare(strict_types=1);

namespace app\services;

use app\models\HabitLog;
use app\models\Task;

/**
 * Collects the numbers for the dashboard. Keeps the SQL/maths out of controllers and views.
 */
class StatsService
{
    /** how many days are used for the completion rate of a habit */
    public const RATE_DAYS = 30;

    public function __construct(private int $userId)
    {
    }

    /**
     * @return array{total: int, done: int, open: int, overdue: int, percent: int}
     */
    public function taskSummary(): array
    {
        $base = Task::find()->where(['user_id' => $this->userId, 'type' => Task::TYPE_TASK]);

        $total = (int)(clone $base)->count();
        $done = (int)(clone $base)->andWhere(['status' => Task::STATUS_DONE])->count();
        $overdue = (int)(clone $base)
            ->andWhere(['status' => Task::STATUS_OPEN])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->count();

        return [
            'total' => $total,
            'done' => $done,
            'open' => $total - $done,
            'overdue' => $overdue,
            'percent' => $total > 0 ? (int)round($done / $total * 100) : 0,
        ];
    }

    /**
     * One row per habit: streak, completion rate and check-ins for the last 7 days.
     * Logs are loaded with a single query, so there is no N+1 problem.
     *
     * @return array<int, array{habit: Task, streak: int, rate: int, today: bool, week: array<string, bool>}>
     */
    public function habitSummary(): array
    {
        $habits = Task::find()
            ->where(['user_id' => $this->userId, 'type' => Task::TYPE_HABIT])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        if (!$habits) {
            return [];
        }

        $rows = HabitLog::find()
            ->select(['task_id', 'log_date'])
            ->where(['task_id' => array_column($habits, 'id')])
            ->asArray()
            ->all();

        $datesByHabit = [];
        foreach ($rows as $row) {
            $datesByHabit[$row['task_id']][$row['log_date']] = true;
        }

        $today = new \DateTimeImmutable('today');
        $summary = [];
        foreach ($habits as $habit) {
            $dates = $datesByHabit[$habit->id] ?? [];

            $week = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = $today->modify("-$i days")->format('Y-m-d');
                $week[$d] = isset($dates[$d]);
            }

            $summary[] = [
                'habit' => $habit,
                'streak' => self::calculateStreak($dates, $today),
                'rate' => $this->completionRate($habit, $dates, $today),
                'today' => isset($dates[$today->format('Y-m-d')]),
                'week' => $week,
            ];
        }

        return $summary;
    }

    /**
     * @param array<string, bool> $dates set of 'Y-m-d' strings (as array keys)
     */
    public static function calculateStreak(array $dates, ?\DateTimeImmutable $today = null): int
    {
        $day = $today ?? new \DateTimeImmutable('today');

        // the day is not over yet - not checked in today should not break the streak
        if (!isset($dates[$day->format('Y-m-d')])) {
            $day = $day->modify('-1 day');
        }

        $streak = 0;
        while (isset($dates[$day->format('Y-m-d')])) {
            $streak++;
            $day = $day->modify('-1 day');
        }
        return $streak;
    }

    /**
     * Percent of days checked in over the last RATE_DAYS days.
     * A habit created 5 days ago is judged by 5 days, not by 30.
     *
     * @param array<string, bool> $dates
     */
    private function completionRate(Task $habit, array $dates, \DateTimeImmutable $today): int
    {
        $createdDay = (new \DateTimeImmutable('@' . $habit->created_at))
            ->setTimezone($today->getTimezone())
            ->setTime(0, 0);
        $age = (int)$createdDay->diff($today)->days + 1;
        $days = max(1, min(self::RATE_DAYS, $age));

        $from = $today->modify('-' . ($days - 1) . ' days')->format('Y-m-d');
        $checked = 0;
        foreach (array_keys($dates) as $date) {
            if ($date >= $from) {
                $checked++;
            }
        }

        return (int)min(100, round($checked / $days * 100));
    }
}
