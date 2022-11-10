<?php

/**
 *   update account and user's addresses
 * @var \common\models\User $user
 * @var \common\models\UserAddress $user_addresses
 * @var \common\models\User $user
 * @var \common\models\UserAddress $userAddress
 * @var \yii\web\View $this
 */

use yii\widgets\Pjax;

?>

<div class="row">
    <div class="col">
        <?php Pjax::begin([
                'enablePushState'=>false
        ]) ?>

            <?php echo $this->render('update_address',[
                    'userAddress'=>$userAddress,
            ]) ?>

        <?php Pjax::end() ?>
    </div>
    <div class="col">
        <?php Pjax::begin([
                'enablePushState'=>false
        ]) ?>

            <?php echo $this->render('update_account',[
                'user'=>$user
            ]) ?>

        <?php Pjax::end() ?>
    </div>
</div>

