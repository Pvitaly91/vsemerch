<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="view">
    <img src="<?=Yii::$app->request->baseUrl.'/upload/web-products/ico/'.$item_p->image?>" alt="<?=Html::encode($item_p->name)?>" width="230" height="250" border="0" />
    <?/**
    <div class="mask">
        <h2><?=$item_p->name?></h2>
        <p><?//=$item_p->about?></p>
        <a href="<?=Url::to(['web-products/show','translit'=>$item_p->translit,'id'=>$item_p->id])?>" class="more"><?=Yii::t('app', 'more')?></a>
    </div>**/?>
</div>
