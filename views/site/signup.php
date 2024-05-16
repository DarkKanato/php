<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\User $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign up';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-4"><?= Html::encode($this->title) ?></h1>

                <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                <?= Html::submitButton('Create account', ['class' => 'btn btn-primary w-100', 'name' => 'signup-button']) ?>

                <?php ActiveForm::end(); ?>

                <p class="mt-3 mb-0 text-center">
                    Already registered? <?= Html::a('Log in', ['/site/login']) ?>
                </p>
            </div>
        </div>
    </div>
</div>
