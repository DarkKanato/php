<?php
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    try {
        $pdo->exec($sql);
        header('Location: login.php');
        exit;
    } catch (Exception $e) {
        $error = 'Cannot register: ' . $e->getMessage();
    }
}

include 'header.php';
?>
<h2>Register</h2>
<?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
    Username:<br>
    <input type="text" name="username"><br>
    Password:<br>
    <input type="password" name="password"><br>
    <input type="submit" value="Register">
</form>
<?php include 'footer.php'; ?>
