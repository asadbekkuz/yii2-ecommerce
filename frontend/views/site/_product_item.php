<?php
/**
 * User: TheCodeholic
 * Date: 12/12/2020
 * Time: 11:53 AM
 */
/** @var \common\models\Product $model */
?>
<div class="card" style="width:304px">
    <img src="<?php echo $model->getImgUrl() ?>" style="width:302px;height: 315px; object-fit: fill" class="card-img-top" alt="...">
    <div class="card-body">
        <h5 class="card-title"><?php echo \yii\helpers\StringHelper::truncateWords($model->name,20) ?></h5>
        <p class="card-text" style="width: 270px;height: 96px"><?php echo $model->getShortDescription() ?></p>
    </div>
    <div class="text-dark opacity-75" style="padding-left: 17px">
        $<?php echo $model->price ?>
    </div>
    <div class="card-body">
        <a href="<?php echo \yii\helpers\Url::to(['/cart/add']) ?>" class="btn btn-primary btn-add-to-cart">
            Add to Cart
        </a>
    </div>
</div>