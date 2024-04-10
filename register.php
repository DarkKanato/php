<?php
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // prepared statement - no more sql injection here
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        try {
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            // don't show raw sql errors to the user
            $error = 'This username is already taken';
        }
    }
}

include 'header.php';
?>
<h2>Register</h2>
<?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<form method="post">
    Username:<br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"><br>
    Password:<br>
    <input type="password" name="password"><br>
    <input type="submit" value="Register">
</form>
<?php include 'footer.php'; ?>
