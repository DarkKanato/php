<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: tasks.php');
    exit;
}

include 'header.php';
?>
<h1>Task Tracker</h1>
<p>Simple app to keep your tasks in one place. Please login or register.</p>
<?php include 'footer.php'; ?>
