<?php

/** @var yii\web\View $this */
/** @var app\models\Task $model */

use app\models\Task;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'priority')->dropDownList(Task::priorityList()) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'due_date')->input('date') ?>
    </div>
</div>

<div class="mt-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-link']) ?>
</div>

<?php ActiveForm::end(); ?>
