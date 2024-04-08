<?php
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $user = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: tasks.php');
        exit;
    } else {
        $error = 'Wrong username or password';
    }
}

include 'header.php';
?>
<h2>Login</h2>
<?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
    Username:<br>
    <input type="text" name="username"><br>
    Password:<br>
    <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>
<?php include 'footer.php'; ?>
