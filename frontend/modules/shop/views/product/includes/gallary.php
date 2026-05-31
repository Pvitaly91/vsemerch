<?
use yii\helpers\Html;
$mainOriginalUrl = $model->getLazyPic('original');
$mainPreviewUrl = $model->getLazyPic('preview');
$mainThumbUrl = $model->getLazyPic('thumb');
?>  


    <? if($model->image):?>
        <a href="<?= $mainOriginalUrl ?>" class="fancybox-big">                            
            <img class="main-img fancybox" 
                data-thumb="<?= $mainThumbUrl ?>"
                data-src="<?= $mainOriginalUrl ?>"
                src="<?= $mainPreviewUrl ?>"
                alt="<?= Html::encode($model->title) ?>"
              <?/*  style="min-width: 441px; max-width:441px"*/?>
            />
         </a>                                
    <? endif;?>
        <div class="review-block__image-alternatives">
            <button class="mini-img"
               data-src="<?= $mainOriginalUrl ?>"
               >
               <img
                    style="max-width:50px" 
                    src="<?= $mainThumbUrl ?>"
                    alt="<?= Html::encode($model->title) ?>"/>
            </button>
        <?php foreach ($model->getGalleryImages() as $image): ?>
            <? if($image->image):?>
               <button
                   class="mini-img"
                   data-src="<?= $image->getLazyPic('original') ?>"
                   >
                   <img style="max-width:50px" src="<?= $image->getLazyPic('ico') ?>"
                        alt="<?= Html::encode($image->alt) ?>"/>
               </button>
            <? endif;?>
        <? endforeach; ?>
       
    </div>
