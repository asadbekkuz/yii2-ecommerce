<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="product-view">

    <h3><?= Html::encode(\yii\helpers\StringHelper::truncateWords($this->title,10)) ?></h3>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute'=>'description',
                'format'=>['html']
            ],
            [
                'attribute'=>'image',
                /** @var \common\models\Product $model */
                'value'=>fn($model)=>Html::img($model->getImgUrl($model->image),['style'=>'width:80px']),
                'format'=>['html']
            ],
            'price',
            [
                'attribute'=>'status',
                'format'=>['html'],
                /** @var \common\models\Product $model */
                'value'=>fn($model)=>$model->getStatus($model->status)
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'createdBy.username',
            'updatedBy.username',
        ],
    ]) ?>

</div>
