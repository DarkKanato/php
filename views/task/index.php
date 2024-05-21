<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $status */

use app\models\Task;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;

$this->title = 'My tasks';
$this->params['breadcrumbs'][] = $this->title;

$priorityClass = [
    Task::PRIORITY_LOW => 'secondary',
    Task::PRIORITY_NORMAL => 'primary',
    Task::PRIORITY_HIGH => 'danger',
];
$today = date('Y-m-d');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('New task', ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<ul class="nav nav-pills mb-3">
    <li class="nav-item"><?= Html::a('All', ['index'], ['class' => 'nav-link' . ($status === null ? ' active' : '')]) ?></li>
    <li class="nav-item"><?= Html::a('Open', ['index', 'status' => 'open'], ['class' => 'nav-link' . ($status === 'open' ? ' active' : '')]) ?></li>
    <li class="nav-item"><?= Html::a('Done', ['index', 'status' => 'done'], ['class' => 'nav-link' . ($status === 'done' ? ' active' : '')]) ?></li>
</ul>

<?php if ($dataProvider->totalCount === 0): ?>
    <div class="alert alert-light border">Nothing here yet. Add your first task!</div>
<?php else: ?>
    <div class="list-group mb-3">
        <?php foreach ($dataProvider->models as $task): ?>
            <?php $overdue = !$task->isDone() && $task->due_date && $task->due_date < $today; ?>
            <div class="list-group-item d-flex align-items-center gap-3">
                <?= Html::beginForm(['toggle', 'id' => $task->id]) ?>
                <?= Html::submitButton(
                    $task->isDone() ? '&#9745;' : '&#9744;',
                    ['class' => 'btn btn-link fs-4 p-0 text-decoration-none', 'title' => 'Toggle']
                ) ?>
                <?= Html::endForm() ?>

                <div class="flex-grow-1">
                    <div class="<?= $task->isDone() ? 'text-decoration-line-through text-body-secondary' : '' ?>">
                        <?= Html::encode($task->title) ?>
                    </div>
                    <?php if ($task->due_date): ?>
                        <small class="<?= $overdue ? 'text-danger' : 'text-body-secondary' ?>">
                            Due <?= Html::encode($task->due_date) ?><?= $overdue ? ' (overdue)' : '' ?>
                        </small>
                    <?php endif; ?>
                </div>

                <span class="badge text-bg-<?= $priorityClass[$task->priority] ?? 'secondary' ?>">
                    <?= Html::encode($task->getPriorityLabel()) ?>
                </span>
                <?= Html::a('Edit', ['update', 'id' => $task->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $task->id], [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'data' => ['method' => 'post', 'confirm' => 'Delete this task?'],
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
<?php endif; ?>
