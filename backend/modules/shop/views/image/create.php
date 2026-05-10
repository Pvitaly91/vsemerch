<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = Yii::t('shop', 'Create Image');
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Image'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a(Yii::t('shop', 'Go to images'), ['index', 'product_id' => $model->product_id], ['class' => 'btn btn-success']) ?>
</p>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
