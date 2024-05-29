<?php

/** @var yii\web\View $this */
/** @var array $tasks */
/** @var array $habits */
/** @var int $bestStreak */
/** @var int $habitsToday */

use yii\bootstrap5\Html;

$this->title = 'Dashboard';
?>
<h1 class="h3 mb-4">Hi, <?= Html::encode(Yii::$app->user->identity->username) ?>!</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-body-secondary small">Open tasks</div>
            <div class="fs-2"><?= $tasks['open'] ?></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-body-secondary small">Overdue</div>
            <div class="fs-2 <?= $tasks['overdue'] ? 'text-danger' : '' ?>"><?= $tasks['overdue'] ?></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-body-secondary small">Habits today</div>
            <div class="fs-2"><?= $habitsToday ?> / <?= count($habits) ?></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-body-secondary small">Best streak</div>
            <div class="fs-2"><?= $bestStreak ?> <small class="fs-6">days</small></div>
        </div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Tasks completed</span>
            <span><?= $tasks['done'] ?> of <?= $tasks['total'] ?> (<?= $tasks['percent'] ?>%)</span>
        </div>
        <div class="progress" role="progressbar" aria-valuenow="<?= $tasks['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar bg-success" style="width: <?= $tasks['percent'] ?>%"></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="h5 mb-0">Habits</h2>
    <?= Html::a('Manage', ['/habit/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
</div>
<?php if (!$habits): ?>
    <div class="alert alert-light border">
        No habits yet. <?= Html::a('Create the first one', ['/habit/create']) ?>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr><th>Habit</th><th>Last 7 days</th><th>Streak</th><th style="width: 30%">Last 30 days</th></tr>
            </thead>
            <tbody>
            <?php foreach ($habits as $row): ?>
                <tr>
                    <td><?= Html::encode($row['habit']->title) ?></td>
                    <td>
                        <div class="habit-week">
                            <?php foreach ($row['week'] as $date => $checked): ?>
                                <span class="habit-dot <?= $checked ? 'habit-dot-on' : '' ?>" title="<?= Html::encode($date) ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td><?= $row['streak'] ?> d</td>
                    <td>
                        <div class="progress" role="progressbar" aria-valuenow="<?= $row['rate'] ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: <?= $row['rate'] ?>%"><?= $row['rate'] ?>%</div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
