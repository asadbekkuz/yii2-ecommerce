<?php  

/**
* @var $order \common\models\Order
* @var $orderAddress \common\models\OrderAddress
* @var $totalPrice \common\models\CartItem
* @var $productQuantity \common\models\CartItem
*/

use yii\helpers\Url;
?>
<script src="https://www.paypal.com/sdk/js?client-id=AT5ZbD4lEn8umpgHB3EyrbVj-yQmD_7kzsEFKOY7QohKyHTG5vEZlS4ipDAyItSRpmwIDvGKPphi3kxn&currency=USD"></script>

<section class="h-100 h-custom" style="background-color: #eee;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-7">
                                <h5 class="mb-3"><a href="#!" class="text-body"><i
                                                class="fas fa-long-arrow-alt-left me-2"></i>Continue shopping</a></h5>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <p class="mb-1">Order #<?= $order->id  ?></p>
                                        <p class="mb-0">You have <?= $productQuantity ?> items in your cart</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">Address</p>
                                    <p class="mb-2"><?= $orderAddress->address ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">City</p>
                                    <p class="mb-2"><?= $orderAddress->city ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">State</p>
                                    <p class="mb-2"><?= $orderAddress->state ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">Country</p>
                                    <p class="mb-2"><?= $orderAddress->country ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">Zipcode</p>
                                    <p class="mb-2"><?= $orderAddress->zipcode ?></p>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card bg-primary text-white rounded-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="mb-0">Payment details</h5>
                                        </div>
                                        <hr class="my-4">
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-2">Subtotal</p>
                                            <p class="mb-2"><?= Yii::$app->formatter->asCurrency($totalPrice) ?></p>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <p class="mb-2">Shipping</p>
                                            <p class="mb-2">$0.00</p>
                                        </div>

                                        <div class="d-flex justify-content-between mb-4">
                                            <p class="mb-2">Total</p>
                                            <p class="mb-2"><?= Yii::$app->formatter->asCurrency($totalPrice) ?></p>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="paypal-button-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({
        // Set up the transaction
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: <?= $totalPrice ?>
                    }
                }]
            });
        },

        // Finalize the transaction
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (orderData) {
                const $form = $("#checkout-form");
                const data = $form.serializeArray();
                // debugger;
                data.push({
                    name:"transantionId",
                    value:orderData.id
                })
                data.push({
                    name:'status',
                    value:orderData.status
                })
                $.ajax({
                    url:'<?php echo Url::to(['/cart/create-order'])?>',
                    method:'POST',
                    data: {data},
                    success:function (res) {
                        console.log(res.errors)
                    }
                })
                // // Successful capture! For demo purposes:
                // console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                alert('Transaction ' + transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            });
        }
    }).render('#paypal-button-container');
</script>