<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('shop', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('shop', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('shop', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'created_at:datetime',
            'updated_at:datetime',
            'name',
            'phone',
            'address',
            [ 
                'attribute' => 'payment',
                'format'=>'raw',
                'value' => function ($model) {
                    return ($model->payment == NULL)?"Післясплата":"<span style='color:green'><strong>Сплачено</strong></span>";
                }
            ],
            'email:email',
            'notes:ntext',
            [
                'filter' => \common\models\Order::getStatuses(),
                'attribute' => 'status',
                'value' => function ($model) {
                    return Yii::t('shop', $model->status);
                }
            ],
        ],
    ]) ?>

    <?= $this->render('_orderItems', [
        'products' => $model->orderItems,
    ]) ?>

</div>
