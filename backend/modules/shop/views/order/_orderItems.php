<?php
use yii\helpers\Html;

?>
<h2><?=Yii::t('shop', 'Orders')?></h2>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-2">
            <?=Yii::t('shop', 'Code')?>
        </div>
        <div class="col-xs-2">
            <?=Yii::t('shop', 'Code Size')?>
        </div>
        <div class="col-xs-4">
            <?=Yii::t('shop', 'Product')?>
        </div>
        <div class="col-xs-1">
            <?=Yii::t('shop', 'Price')?>
        </div>
        <div class="col-xs-1">
            <?=Yii::t('shop', 'Quantity')?>
        </div>
        <div class="col-xs-2">
            <?=Yii::t('shop', 'Cost')?>
        </div>
    </div>
    <?php $sum = 0;
        foreach ($products as $product):
        ?>
        <?php $sum += $product->quantity * $product->price ?>
        <div class="row">
            <div class="col-xs-2">
                <?= Html::encode($product->product->partner_id) ?>
            </div>
            <div class="col-xs-2">
                <?= Html::encode($product->code) ?>
            </div>
            <div class="col-xs-4">
                <?= Html::encode($product->title) ?>
            </div>
            <div class="col-xs-1">
                <?= $product->price ?> <?=Yii::t('shop', 'UAH')?>
            </div>
            <div class="col-xs-1">
                <?= $quantity = $product->quantity ?>
            </div>
            <div class="col-xs-2">
                <?= $product->quantity * $product->price ?> <?=Yii::t('shop', 'UAH')?>
            </div>
        </div>
    <?php endforeach ?>
    <div class="row">
        <div class="col-xs-10">

        </div>
        <div class="col-xs-2">
            <?=Yii::t('shop', 'Total')?>: <strong><?= $sum ?> <?=Yii::t('shop', 'UAH')?></strong>
        </div>
    </div>
</div>
