<?php

/**
* @var \common\models\CartItem $item
*/

use common\models\Product;

?>
<div class="card">
    <div class="card-header text-center">
        Your Cart Page
    </div>
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
            <?php foreach ($item as $value): ?>
                <tr>
                    <td class="text-center"><?php echo $value['name'] ?></td>
                    <td>
                        <img
                                src="<?php echo Product::formatImageUrl($value['image']) ?>"
                                alt="<?php echo $value['name'] ?>"
                                style="width: 50px;">
                    </td>
                    <td><?php echo $value['price'] ?></td>
                    <td><?php echo $value['quantity'] ?></td>
                    <td><?php echo $value['total_price'] ?></td>
                    <td>
                        <?php echo \yii\helpers\Html::a('Delete',['/cart/delete'],[
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
        <?php echo \yii\helpers\Html::a('Delete',['/cart/checkout'],[
                'class'=>'btn btn-primary '
        ]) ?>
    </div>
</div>
