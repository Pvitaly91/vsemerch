<!--Product Description-->
<div class="product-description">
    <h1 class="main-title"><?=$title?></h1>
    <div <? if($flag == 1):?>class="hide-if-mob"<? endif;?>>
        <p><?=Yii::t('shop', 'Code')?>: <span class="code product-code" id="codeBox"><?=$model->code?></span></p>
        <div class="product-description__size" id="mob-size-block">
            <? include 'size.php';?>
        </div>
        <div id="mob-avail-block">   
            <? include 'avail_block.php'?>
        </div>
    </div>
</div>