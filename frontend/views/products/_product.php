<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="view4">
    <a href="<?= Yii::$app->request->baseUrl . '/upload/products/' . $item_p->image ?>" rel="shadowbox[gal1]"
       class="fancybox" title="<?= Html::encode($item_p->name) ?>">
        <img src="<?= Yii::$app->request->baseUrl . '/upload/products/ico/' . $item_p->image ?>" alt="<?= Html::encode($item_p->name) ?>" width="255" height="170" border="0"/>
    </a>
    <?/**
    <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox"
           href="<?= Yii::$app->request->baseUrl . '/upload/products/' . $item_p->image ?>"><?= $item_p->name ?>
        </a>
    </div>**/?>


</div>
