<h2>Login</h2>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
    Username:<br>
    <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"><br>
    Password:<br>
    <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>
