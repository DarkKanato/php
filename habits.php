<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];
$today = date('Y-m-d');

// add habit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty(trim($_POST['title'] ?? ''))) {
    $stmt = $pdo->prepare('INSERT INTO habits (user_id, title) VALUES (?, ?)');
    $stmt->execute([$uid, trim($_POST['title'])]);
    header('Location: habits.php');
    exit;
}

// check-in for today
if (isset($_GET['checkin'])) {
    $habitId = (int)$_GET['checkin'];

    // make sure the habit belongs to current user
    $stmt = $pdo->prepare('SELECT id FROM habits WHERE id = ? AND user_id = ?');
    $stmt->execute([$habitId, $uid]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT OR IGNORE INTO habit_logs (habit_id, log_date) VALUES (?, ?)');
        $stmt->execute([$habitId, $today]);
    }
    header('Location: habits.php');
    exit;
}

if (isset($_GET['delete'])) {
    $habitId = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM habit_logs WHERE habit_id IN (SELECT id FROM habits WHERE id = ? AND user_id = ?)')
        ->execute([$habitId, $uid]);
    $pdo->prepare('DELETE FROM habits WHERE id = ? AND user_id = ?')->execute([$habitId, $uid]);
    header('Location: habits.php');
    exit;
}

$stmt = $pdo->prepare('SELECT h.*,
    (SELECT COUNT(*) FROM habit_logs l WHERE l.habit_id = h.id AND l.log_date = ?) AS checked_today
    FROM habits h WHERE h.user_id = ? ORDER BY h.id DESC');
$stmt->execute([$today, $uid]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<h2>My habits</h2>
<form method="post">
    <input type="text" name="title" placeholder="New habit, e.g. 'Read 20 pages'">
    <input type="submit" value="Add">
</form>
<ul>
<?php foreach ($habits as $h): ?>
    <li>
        <?php echo htmlspecialchars($h['title']); ?>
        <?php if ($h['checked_today']): ?>
            <span class="done">done today</span>
        <?php else: ?>
            <a href="?checkin=<?php echo $h['id']; ?>">[check in]</a>
        <?php endif; ?>
        <a href="?delete=<?php echo $h['id']; ?>">[x]</a>
    </li>
<?php endforeach; ?>
</ul>
<?php include 'footer.php'; ?>
