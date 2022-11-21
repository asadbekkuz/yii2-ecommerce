<?php

/**
* @var \common\models\CartItem $items
*/

use common\models\Product;
?>
<div class="card">
    <div class="card-header text-center fw-bold">
        <h5>Your Cart Items</h5>
    </div>
    <?php if(!empty($items)): ?>
        <div class="card-body">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th class="text-center">Name</th>
                    <th>Image</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr class="row-item-quantity" data-key="<?php echo $item['id'] ?>" data-url="<?php echo \yii\helpers\Url::to(['/cart/change-quantity']) ?>">
                        <td class="text-center"><?php echo $item['name'] ?></td>
                        <td>
                            <img
                                    src="<?php echo Product::formatImageUrl($item['image']) ?>"
                                    alt="<?php echo $item['name'] ?>"
                                    style="width: 50px;">
                        </td>
                        <td><?php echo $item['price'] ?></td>
                        <td>
                            <input
                                    type="number"
                                    min="1"
                                    class="form-control cart-item-quantity"
                                    style="width:64px"
                                    value="<?php echo $item['quantity'] ?>">
                        </td>
                        <td><?php echo $item['total_price'] ?></td>
                        <td>
                            <?php echo \yii\helpers\Html::a('Delete',['/cart/delete','id'=>$item['id']],[
                                'class'=>'btn btn-sm btn-outline-danger',
                                'data-method'=>'post',
                                'data-confirm'=> 'Are you sure to remove product item from cart ?'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-body text-end">
            <?php echo \yii\helpers\Html::a('Checkout',\yii\helpers\Url::to(['/cart/checkout']),[
                    'class'=>'btn btn-primary '
            ]) ?>
        </div>
    <?php else: ?>
        <div class="card-body">
            <h5 class="text-muted p-5 text-center">There no items in the Cart</h5>
        </div>
    <?php endif; ?>
</div>
