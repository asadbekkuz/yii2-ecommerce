<?php



use yii\bootstrap5\ActiveForm;

/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var \common\models\Product $productQuantity */
/** @var \common\models\Product $totalPrice */

?>
<script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD"></script>

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
                    <div id="paypal-button-container"></div>
<!--                    <div class="card-body text-end">-->
<!--                        --><?php //echo \yii\helpers\Html::submitButton('Checkout',['class'=>'btn btn-secondary']) ?>
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script>
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({

        // Set up the transaction
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '88.44'
                    }
                }]
            });
        },

        // Finalize the transaction
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(orderData) {
                // Successful capture! For demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                alert('Transaction '+ transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            });
        }


    }).render('#paypal-button-container');
</script>