<?php

/** @var yii\web\View $this */
/** @var array $habits rows from StatsService::habitSummary() */

use yii\bootstrap5\Html;

$this->title = 'My habits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('New habit', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?php if (!$habits): ?>
    <div class="alert alert-light border">No habits yet. Pick something small and start with it today.</div>
<?php endif; ?>

<div class="list-group">
    <?php foreach ($habits as $row): ?>
        <?php $habit = $row['habit']; ?>
        <div class="list-group-item d-flex align-items-center gap-3 flex-wrap">
            <div class="flex-grow-1">
                <div class="fw-semibold"><?= Html::encode($habit->title) ?></div>
                <small class="text-body-secondary">
                    Streak: <?= $row['streak'] ?> d &middot; 30 days: <?= $row['rate'] ?>%
                </small>
            </div>

            <div class="habit-week" title="Last 7 days">
                <?php foreach ($row['week'] as $date => $checked): ?>
                    <span class="habit-dot <?= $checked ? 'habit-dot-on' : '' ?>" title="<?= Html::encode($date) ?>"></span>
                <?php endforeach; ?>
            </div>

            <?= Html::beginForm(['checkin', 'id' => $habit->id]) ?>
            <?= Html::submitButton(
                $row['today'] ? 'Done today' : 'Check in',
                ['class' => 'btn btn-sm ' . ($row['today'] ? 'btn-success' : 'btn-outline-success')]
            ) ?>
            <?= Html::endForm() ?>

            <?= Html::a('Edit', ['update', 'id' => $habit->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $habit->id], [
                'class' => 'btn btn-sm btn-outline-danger',
                'data' => ['method' => 'post', 'confirm' => 'Delete this habit and all its history?'],
            ]) ?>
        </div>
    <?php endforeach; ?>
</div>
