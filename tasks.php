<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

// mark as done
if (isset($_GET['done'])) {
    $pdo->exec("UPDATE tasks SET done = 1 WHERE id = " . $_GET['done']);
    header('Location: tasks.php');
    exit;
}

// delete
if (isset($_GET['delete'])) {
    $pdo->exec("DELETE FROM tasks WHERE id = " . $_GET['delete']);
    header('Location: tasks.php');
    exit;
}

// add new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['title'])) {
    $title = $_POST['title'];
    $pdo->exec("INSERT INTO tasks (user_id, title) VALUES ($uid, '$title')");
    header('Location: tasks.php');
    exit;
}

$tasks = $pdo->query("SELECT * FROM tasks WHERE user_id = $uid ORDER BY done, id DESC")->fetchAll(PDO::FETCH_ASSOC);

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
        <?php echo $t['title']; ?>
        <?php if (!$t['done']): ?><a href="?done=<?php echo $t['id']; ?>">[done]</a><?php endif; ?>
        <a href="?delete=<?php echo $t['id']; ?>">[x]</a>
    </li>
<?php endforeach; ?>
</ul>
<?php include 'footer.php'; ?>
