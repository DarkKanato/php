<h2>My tasks</h2>
<form method="post" action="index.php?r=task/create">
    <input type="text" name="title" placeholder="What should be done?">
    <input type="submit" value="Add">
</form>
<ul>
<?php foreach ($tasks as $t): ?>
    <li class="<?= $t['done'] ? 'done' : '' ?>">
        <?= htmlspecialchars($t['title']) ?>
        <?php if (!$t['done']): ?><a href="index.php?r=task/done&id=<?= $t['id'] ?>">[done]</a><?php endif; ?>
        <a href="index.php?r=task/delete&id=<?= $t['id'] ?>">[x]</a>
    </li>
<?php endforeach; ?>
</ul>
