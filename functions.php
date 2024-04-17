<?php
// functions.php - helpers for statistics

/**
 * Returns current streak (days in a row) for a habit.
 */
function getStreak($pdo, $habitId)
{
    $stmt = $pdo->prepare('SELECT log_date FROM habit_logs WHERE habit_id = ? ORDER BY log_date DESC');
    $stmt->execute([$habitId]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $streak = 0;
    $day = new DateTime('today');
    while (in_array($day->format('Y-m-d'), $dates)) {
        $streak++;
        $day->modify('-1 day');
    }
    return $streak;
}

/**
 * Percent of days (out of last $days) when habit was done.
 */
function completionRate($pdo, $habitId, $days = 30)
{
    $from = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM habit_logs WHERE habit_id = ? AND log_date >= ?');
    $stmt->execute([$habitId, $from]);
    return round($stmt->fetchColumn() / $days * 100);
}
