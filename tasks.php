<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

// mark as done (only own tasks!)
if (isset($_GET['done'])) {
    $stmt = $pdo->prepare('UPDATE tasks SET done = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([(int)$_GET['done'], $uid]);
    header('Location: tasks.php');
    exit;
}

// delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([(int)$_GET['delete'], $uid]);
    header('Location: tasks.php');
    exit;
}

// add new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty(trim($_POST['title'] ?? ''))) {
    $stmt = $pdo->prepare('INSERT INTO tasks (user_id, title) VALUES (?, ?)');
    $stmt->execute([$uid, trim($_POST['title'])]);
    header('Location: tasks.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY done, id DESC');
$stmt->execute([$uid]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<h2>My tasks</h2>
<form method="post">
    <input type="text" name="title" placeholder="What should be done?">
    <input type="submit" value="Add">
</form>
<ul>
<?php foreach ($tasks as $t): ?>
    <li class="<?php echo $t['done'] ? 'done' : ''; ?>">
        <?php echo htmlspecialchars($t['title']); ?>
        <?php if (!$t['done']): ?><a href="?done=<?php echo $t['id']; ?>">[done]</a><?php endif; ?>
        <a href="?delete=<?php echo $t['id']; ?>">[x]</a>
    </li>
<?php endforeach; ?>
</ul>
<?php include 'footer.php'; ?>
