<?php

/** @var yii\web\View $this */

/** @var \yii\data\ActiveDataProvider $dataProvider */

use yii\widgets\ListView;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?php
    echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_product_item',
        'options' => [
            'class'=>'row'
        ],
        'layout'=>'{items}{pager}',
        'itemOptions' => [
            'class' => 'col-lg-4 col-md-6 mb-4 product-item'
        ],
        'pager'=>[
                'class'=>\yii\bootstrap5\LinkPager::class,
                'prevPageLabel'=>'Prev',
                'nextPageLabel'=>'Next'
        ]
    ]);
    ?>
</div>
