<h2>My habits</h2>
<form method="post" action="index.php?r=habit/create">
    <input type="text" name="title" placeholder="New habit, e.g. 'Read 20 pages'">
    <input type="submit" value="Add">
</form>
<ul>
<?php foreach ($habits as $h): ?>
    <li>
        <?= htmlspecialchars($h['title']) ?>
        <?php if ($h['checked_today']): ?>
            <span class="done">done today</span>
        <?php else: ?>
            <a href="index.php?r=habit/checkin&id=<?= $h['id'] ?>">[check in]</a>
        <?php endif; ?>
        <a href="index.php?r=habit/delete&id=<?= $h['id'] ?>">[x]</a>
    </li>
<?php endforeach; ?>
</ul>
