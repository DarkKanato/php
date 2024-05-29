<?php

/** @var yii\web\View $this */
/** @var app\models\Task $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true, 'autofocus' => true, 'placeholder' => 'e.g. Read 20 pages']) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

<div class="mt-3">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-link']) ?>
</div>

<?php ActiveForm::end(); ?>
