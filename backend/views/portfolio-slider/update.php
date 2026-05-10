<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->title = 'Обновить ' . $model->sort;
$this->params['breadcrumbs'][] = ['label' => 'Портфолио слайдер', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sort, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('shop', 'Update');
?>
<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
    ]) ?>

</div>
