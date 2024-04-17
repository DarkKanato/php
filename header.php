<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nav">
    <a href="index.php">Home</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="tasks.php">Tasks</a>
        <a href="habits.php">Habits</a>
        <a href="stats.php">Stats</a>
        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</div>
<div class="content">
