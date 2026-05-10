<?php use yii\helpers\Html; ?>
<div class="product-card-block__about">
 
    <div class="content"  >

    <?php /*if(!empty($model->fotos)):?>
        <div class="box3">
            <div class="container">
                <div class="text-center"><h3><?=Yii::t('app', 'OUR WORK')?></h3></div>
                <div class="slider1">
                    <?php foreach($model->fotos as $item):?>
                        <div class="slide">
                            <div class="view4">
                                <a href="<?=Yii::$app->request->baseUrl.'/upload/text_fotos/'.$item->image?>" rel="shadowbox[gal1]" class="fancybox" title="<?=Html::encode($item->name)?>">
                                    <img src="<?=Yii::$app->request->baseUrl.'/upload/text_fotos/ico/'.$item->image?>" alt="<?=Html::encode($item->name)?>" width="255" height="170" border="0" />
                                </a>
                             
                            </div>
                        </div>
                    <?php endforeach; ?>


                </div>
            </div>
        </div>
    <?php endif;*/?>

       
        <h1><?= $title; ?></h1>
        <div class="preview_text">
            <?=(isset($_body))?$_body:$body; ?>
        </div>    
            <?php //=$model->body;?>
    </div>
</div>
