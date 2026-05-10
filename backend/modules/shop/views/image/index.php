<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ImageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form backend\models\MultipleUploadForm */

$this->title = $searchModel->product_id ? Yii::t('shop', "Product #").$searchModel->product_id . Yii::t('shop', "images") : Yii::t('shop', 'Images');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('shop', 'Create Image'), ['create', 'product_id' => $product_id], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    /** @var $model common\models\Image */
                    return Html::img($model->getImageFileUrl('image', 'thumb'), ['width' => 200]);
                }
            ],
            'alt',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-label="Image"></span>', Url::to(['image/view', 'id' => $model->id, 'product_id' => $model->product_id]));
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-label="Image"></span>', Url::to(['image/update', 'id' => $model->id, 'product_id' => $model->product_id]));
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash" aria-label="Image"></span>', Url::to(['image/delete', 'id' => $model->id, 'product_id' => $model->product_id]),[
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>

</div>
