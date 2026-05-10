<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShopSize */

$this->title = 'Create Shop Size';
$this->params['breadcrumbs'][] = ['label' => 'Shop Sizes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-size-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
