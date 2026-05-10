<?
use yii\helpers\Html;
?>  


    <? if($model->getPic('image', 'thumb', '/img/no_image.jpg') && $model->getImageFileUrl('image') &&  $model->getPic('image', 'preview', '/img/no_image.jpg')):?>
        <a href="<?= $model->getBigImg() ?>" class="fancybox-big">                            
            <img class="main-img fancybox" 
                data-thumb="<?= $model->getPic('image', 'thumb', '/img/no_image.jpg') ?>"
                data-src="<?= $model->getImageFileUrl('image') ?>"
                src="<?= $model->getPic('image', 'preview', '/img/no_image.jpg') ?>"
                alt="<?= Html::encode($model->title) ?>"
              <?/*  style="min-width: 441px; max-width:441px"*/?>
            />
         </a>                                
    <? endif;?>
        <div class="review-block__image-alternatives">
            <button class="mini-img"
               data-src="<?= $model->getImageFileUrl('image') ?>"
               >
               <img
                    style="max-width:50px" 
                    src="<?= $model->getPic('image', 'thumb', '/img/no_image.jpg') ?>"
                    alt="<?=(!empty($image))? Html::encode($image->alt):"" ?>"/>
            </button>
        <?php foreach ($model->images as $image): ?>     
            <? if($image->getThumbFileUrl('image', 'ico', '/img/no_image.jpg') && $image->getImageFileUrl('image') &&  $image->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg')):?>
               <button
                   class="mini-img"
                   data-src="<?= $image->getImageFileUrl('image') ?>"
                   >
                   <img style="max-width:50px" src="<?= $image->getThumbFileUrl('image', 'ico', '/img/no_image.jpg') ?>"
                        alt="<?= Html::encode($image->alt) ?>"/>
               </button>
            <? endif;?>
        <? endforeach; ?>
       
    </div>
