<?php
require 'db.php';
require 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT COUNT(*) AS total, SUM(done) AS done FROM tasks WHERE user_id = ?');
$stmt->execute([$uid]);
$taskStats = $stmt->fetch(PDO::FETCH_ASSOC);
$taskPercent = $taskStats['total'] > 0 ? round($taskStats['done'] / $taskStats['total'] * 100) : 0;

$stmt = $pdo->prepare('SELECT * FROM habits WHERE user_id = ?');
$stmt->execute([$uid]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<h2>Statistics</h2>

<h3>Tasks</h3>
<p>Done <?php echo (int)$taskStats['done']; ?> of <?php echo (int)$taskStats['total']; ?> (<?php echo $taskPercent; ?>%)</p>
<div class="bar"><div class="bar-fill" style="width: <?php echo $taskPercent; ?>%"></div></div>

<h3>Habits (last 30 days)</h3>
<?php if (!$habits): ?>
    <p>No habits yet.</p>
<?php endif; ?>
<table>
    <tr><th>Habit</th><th>Streak</th><th>Completion</th></tr>
<?php foreach ($habits as $h): ?>
    <tr>
        <td><?php echo htmlspecialchars($h['title']); ?></td>
        <td><?php echo getStreak($pdo, $h['id']); ?> d.</td>
        <td><?php echo completionRate($pdo, $h['id']); ?>%</td>
    </tr>
<?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
