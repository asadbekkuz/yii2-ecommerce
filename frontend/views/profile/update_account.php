<?php

use yii\bootstrap5\ActiveForm;

/** @var \common\models\User $user */
?>


<div class="card">
    <div class="card-header text-center">
        Account information
    </div>

    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success text-center">
            You account was successfully updated
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'action' => 'update-account',
            'options' => [
                'data-pjax' => 1
            ]
        ]); ?>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($user, 'lastname')->textInput(['autofocus' => true]) ?>
            </div>
        </div>

        <?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($user, 'email') ?>

        <div class="row">
            <div class="col">
                <?= $form->field($user, 'password')->passwordInput() ?>
            </div>
            <div class="col">
                <?= $form->field($user, 'passwordConfirm')->passwordInput() ?>
            </div>
        </div>

        <button class="btn btn-primary">Update</button>

        <?php ActiveForm::end(); ?>
    </div>
</div>