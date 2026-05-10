<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('shop', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('shop', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $treeSelect = new \alex290\treeselect\TreeSelect();
    $categories = \common\models\Category::asArray();

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var $model common\models\Image */
                    return Html::img($model->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg'), ['width' => 100]);
                }
            ],
            'code',
            [
                'attribute' => 'title',
                'contentOptions' => ['style' => 'width:400px; white-space: normal;'],
            ],
            [
                'filter' => $treeSelect->getTree($categories),
                'attribute' => 'category_id',
                'value' => function ($model) {
                    return empty($model->category_id) ? '-' : $model->category->title;
                },
            ],
            'price',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {images} {delete}',
                'buttons' => [
                    'images' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon glyphicon-picture" aria-label="Image"></span>', Url::to(['image/index', 'product_id' => $model->id]));
                    }
                ],
            ],
        ],
    ]); ?>

</div>
