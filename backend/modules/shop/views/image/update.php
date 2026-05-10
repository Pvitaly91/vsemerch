<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = Yii::t('shop', 'Update Image: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Image'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('shop', 'Update');
?>
<p>
    <?= Html::a(Yii::t('shop', 'Go to images'), ['index', 'product_id' => $model->product_id], ['class' => 'btn btn-success']) ?>
</p>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
