<?php

use common\models\Product;
use yii\helpers\Html;
use common\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

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
            [
                'attribute'=>'name',
                'contentOptions'=>[
                        'width'=>'200px'
                ]
            ],
            [
                'attribute'=>'image',
                'content'=>function($model){
                    /**
                     * @var \common\models\Product $model
                     */
                    return Html::img($model->getImgUrl($model->image),[
                            'width'=>'120px'
                    ]);
                }
            ],
            [
                'attribute'=>'price',
                'content'=>function($model){
                    return Yii::$app->formatter->asCurrency($model->price);
                }
            ],
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
//            'updated_at:datetime',
            [
                'class' => ActionColumn::className(),
                'contentOptions'=>['width'=>'150px'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    //view button
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                            'title' => Yii::t('app', 'View'),
                            'class'=>'btn btn-outline-primary btn-sm',
                        ]);
                    },
                    //update button
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-pen"></i>', $url, [
                            'title' => Yii::t('app', 'Update'),
                            'class'=>'btn btn-outline-success btn-sm',
                        ]);
                    },
                    //delete button
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => Yii::t('app', 'Delete'),
                            'class'=>'btn btn-outline-danger btn-sm',
                        ]);
                    },
                ],
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
