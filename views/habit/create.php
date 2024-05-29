<?php

/** @var yii\web\View $this */
/** @var app\models\Task $model */

use yii\bootstrap5\Html;

$this->title = 'New habit';
$this->params['breadcrumbs'][] = ['label' => 'Habits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h1 class="h3 mb-4"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>
