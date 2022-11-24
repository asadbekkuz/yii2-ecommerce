<?php



use yii\bootstrap5\ActiveForm;

/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var \common\models\Product $productQuantity */
/** @var \common\models\Product $totalPrice */

?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>
                </div>
                <?= $form->field($order, 'email') ?>

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Address Information</h5>
            </div>
            <div class="card-body">
                <?= $form->field($orderAddress, 'address')->textInput(['autofocus' => true]) ?>

                <?= $form->field($orderAddress, 'city')->textInput(['autofocus' => true]) ?>

                <?= $form->field($orderAddress, 'state')->textInput(['autofocus' => true]) ?>

                <?= $form->field($orderAddress, 'country')->textInput(['autofocus' => true]) ?>

                <?= $form->field($orderAddress, 'zipcode')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5>Order Summary</h5>
                </div>
                <div class="card-body">
                    <div>
                        <table class="table">
                            <tr>
                                <td>Products</td>
                                <td class="text-right ms-auto"> <?php echo $productQuantity ?></td>
                            </tr>
                            <tr>
                                <td>Total Price</td>
                                <td class="text-right">
                                    <?php echo Yii::$app->formatter->asCurrency($totalPrice) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body text-end">
                        <?php echo \yii\helpers\Html::submitButton('Checkout',['class'=>'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
