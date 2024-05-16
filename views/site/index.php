<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = Yii::$app->name;
?>
<div class="site-index text-center py-5">
    <h1 class="display-5 mb-3">Task &amp; Habit Tracker</h1>
    <p class="lead text-body-secondary">
        Keep your to-do list and your daily habits in one place and see how you are doing.
    </p>
    <p>
        <?= Html::a('Create account', ['/site/signup'], ['class' => 'btn btn-primary btn-lg me-2']) ?>
        <?= Html::a('Log in', ['/site/login'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
    </p>
</div>
