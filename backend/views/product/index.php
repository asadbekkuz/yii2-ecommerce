<?php

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\Product $model */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'contentOptions'=>[
                        'width'=>'60px'
                ]
            ],
            'name',
            [
                'attribute'=>'image',
                'content'=>function($model){
                    /**
                     * @var \common\models\Product $model
                     */
                    return Html::img($model->getImgUrl(),[
                            'style'=>'width:80px'
                    ]);
                }
            ],
            'price',
            [
                'attribute'=>'status',
                'content'=>function($model){
                    /**
                     * @var \common\models\Product $model
                     */
                    return $model->getStatus($model->status);
                },
                'contentOptions'=>[
                        'width'=>'40px'
                ]
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
