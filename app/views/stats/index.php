<h2>Statistics</h2>

<h3>Tasks</h3>
<p>Done <?= $tasks['done'] ?> of <?= $tasks['total'] ?> (<?= $tasks['percent'] ?>%)</p>
<div class="bar"><div class="bar-fill" style="width: <?= $tasks['percent'] ?>%"></div></div>

<h3>Habits (last 30 days)</h3>
<?php if (!$habits): ?>
    <p>No habits yet.</p>
<?php endif; ?>
<table>
    <tr><th>Habit</th><th>Streak</th><th>Completion</th></tr>
<?php foreach ($habits as $h): ?>
    <tr>
        <td><?= htmlspecialchars($h['title']) ?></td>
        <td><?= $h['streak'] ?> d.</td>
        <td><?= $h['rate'] ?>%</td>
    </tr>
<?php endforeach; ?>
</table>
