<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $categories common\models\Category[] */

$this->title = Yii::t('shop', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('shop', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <h1><?=($catNama != '')?" Создать под категорию для: ".$catNama: Html::encode($this->title)?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'pageList' => $pageList,
        "partner" => $partner
    ]) ?>

</div>
