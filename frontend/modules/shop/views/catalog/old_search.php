<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;

/* @var $this yii\web\View */
$title = Yii::t('shop', 'Product Search');
$this->title = Html::encode($title);

?>

<div class="head2 3">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= Html::encode($title) ?></h1>

            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        ['label' => Yii::t('shop', 'Shop')],
                        $title
                    ],
                ]) ?>
            </nav>


        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1><?= Yii::t('shop', 'Product Search') ?></h1>
                </div>
                <div class="col-md-4">
                    <?php
                    $url = rawurldecode(\yii\helpers\Url::to(['/shop/catalog/search', 'view-sort' => 'all', 'search_str' => Yii::$app->request->get('search_str')]));
                    $items = [
                        'price' => Yii::t('shop', 'From cheap to expensive'),
                        '-price' => Yii::t('shop', 'From expensive to cheap'),
                        '-novelty' => Yii::t('shop', 'Novelty'),
                        '-action' => Yii::t('shop', 'Actions'),
                    ];
                    echo Html::dropDownList('sort', $sort, $items, ['prompt' => Yii::t('shop', 'Choose sort'), 'onchange' => "document.location='{$url}&sort='+this.value;", 'class' => 'form-control']);
                    ?>
                </div>
            </div>
            <div class="row">
                <?= ListView::widget([
                    'dataProvider' => $productsDataProvider,
                    'itemView' => '_product_search',
                ]) ?>
            </div>
        </div>
    </div>
</div>