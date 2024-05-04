<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nav">
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?r=task/index">Tasks</a>
        <a href="index.php?r=habit/index">Habits</a>
        <a href="index.php?r=stats/index">Stats</a>
        <a href="index.php?r=auth/logout">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
    <?php else: ?>
        <a href="index.php?r=auth/login">Login</a>
        <a href="index.php?r=auth/register">Register</a>
    <?php endif; ?>
</div>
<div class="content">
    <?php if (!empty($_SESSION['flash'])): ?>
        <p class="flash flash-<?= $_SESSION['flash']['type'] ?>"><?= htmlspecialchars($_SESSION['flash']['message']) ?></p>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?= $content ?>
</div>
</body>
</html>
