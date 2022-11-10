<?php

use yii\bootstrap5\ActiveForm;

/** @var \common\models\UserAddress $userAddress */
?>


<div class="card">
    <div class="card-header text-center">
        Address information
    </div>
    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success text-center">
            You address was successfully updated
        </div>
    <?php endif; ?>
    <div class="card-body">
        <?php $userAddresses = ActiveForm::begin([
                'action' => 'update-address',
                'options'=>[
                    'data-pjax'=>1
                ]
        ]); ?>

        <?= $userAddresses->field($userAddress, 'address')->textInput(['autofocus' => true]) ?>

        <?= $userAddresses->field($userAddress, 'city')->textInput(['autofocus' => true]) ?>

        <?= $userAddresses->field($userAddress, 'state')->textInput(['autofocus' => true]) ?>

        <?= $userAddresses->field($userAddress, 'country')->textInput(['autofocus' => true]) ?>

        <?= $userAddresses->field($userAddress, 'zipcode')->textInput(['autofocus' => true]) ?>

        <button class="btn btn-primary">Update</button>

        <?php ActiveForm::end(); ?>
    </div>
</div>