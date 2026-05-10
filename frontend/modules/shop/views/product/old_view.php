<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;

/* @var $this yii\web\View */
$title = $model->title;
$this->title = Html::encode($model->meta_title);
$this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
//$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonical]);

$this->registerCssFile("/js/lightGallery/src/css/lightgallery.css");
$this->registerJsFile('/js/lightGallery/src/js/lightgallery.js', ['position' => yii\web\View::POS_END, 'depends' => ['yii\web\JqueryAsset']]);

$this->registerCssFile("/js/lightslider/src/css/lightslider.css");
$this->registerJsFile('/js/lightslider/src/js/lightslider.js', ['position' => yii\web\View::POS_END, 'depends' => ['yii\web\JqueryAsset']]);

if(isset($_GET["size"]) && is_numeric($_GET["size"])){
    $script = " var size = '".$_GET["size"]."'; ";
}else{
    $script = " var size = null ";
}
$this->registerJs($script, yii\web\View::POS_READY);  
$script = <<< JS
        
        function makeGallery(){
            $('.vertical').lightSlider({
                gallery:true,
                item:1,
                loop:true,
                thumbItem:9,
                vertical:true,
                verticalHeight:455,
                vThumbWidth:50,
                slideMargin:0,
                enableDrag: false,
                currentPagerPosition:'left',
                onSliderLoad: function(el) { 
                    el.lightGallery({
                        selector: '#vertical .lslide'
                    }); 
                },
                onBeforeSlide: function(el, scene) {
                  el.children('li').each(function(index) {
                      if(scene==index){  
                      //alert($(this).data('src'));
                      }
                  })
                }  
            });
        }
       
        $('.skulink').click(function(event){
            event.preventDefault();
            $('.skuGal').addClass('hide');
            $('.sku_gal_'+$(this).attr('data-id')).removeClass('hide');
            $('.sku').removeClass("activeColor");
            $("#color_"+$(this).attr('data-id')).addClass("activeColor");
            
            history.pushState(null, null, $(this).attr("href"));
            $.get( "/ajax/productdata?id="+$(this).attr('data-id'), function( data ) {
                if(data["success"] == true){
                    $("#ajax-body").html(data["html"]);
                    $("#bottom-script").html(data["script"]);
                    $("#ajax-desc").html(data["description"]); 
                    $("#ajax-option").html(data["option"]);      
                    $("#ajax-gallery").html(data["gallery"]);  
                    makeGallery();                     
                    if(size != null){
                        $("a[data-id="+size+"]").trigger("click");
                    }
               
                }
                
            });
         });
        
         makeGallery(); 
JS;

$this->registerJs($script, yii\web\View::POS_READY);

     
?>

    
<style>
    #colorBlock{
        margin-top: 15px;
        margin-bottom: 10px;
        margin-left: 5px;
    }
    .activeColor{
        border:2px solid red;
        border-radius: 5px;
    }
    .sku{
        width:56px;
        margin:2px;
        
    }
    #titleBlock{
        
         width: 100%;
    }
    .skulink,.skulink:hover,.skulink:active,.skulink:visited {
        text-decoration: none;
    }
    
    #titleBlock h1{
     
        margin-bottom: 15px;
        margin-left: calc(50% + 15px);
        width: 450px;
    }
    @media only screen and (min-width: 991px){
        #galBlock{
            margin-top: -55px;
            
        }
      
    }
    @media only screen and (max-width: 991px){
        h1{ 
            width: 100% !important;
            margin-left: 0px !important;
        };
     
    }
    .size-box.disabled{
        background-color: #eee;
        cursor:not-allowed;
    }
    
</style>
<div class="head2 5">
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
                        ['label' => $model->category->title, 'url' => ['catalog/list', 'slug' => $model->category->slug, 'id' => $model->category->id]],
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
            <div class="well">
                <div id="titleBlock">
                    <h1><?= $model->title  ?></h1>
                   
                </div>
               
                <div class="row">
                    <div class="col-md-6" id="galBlock">
                        <div class="mainGal" id="ajax-gallery">
                            <ul  class="vertical" >
                            
                                    <? if($model->getPic('image', 'thumb', '/img/no_image.jpg') && $model->getImageFileUrl('image') &&  $model->getPic('image', 'preview', '/img/no_image.jpg')):?>
                                        <li data-thumb="<?= $model->getPic('image', 'thumb', '/img/no_image.jpg') ?>"
                                            data-src="<?= $model->getImageFileUrl('image') ?>">
                                            <img src="<?= $model->getPic('image', 'preview', '/img/no_image.jpg') ?>"
                                                 alt="<?= Html::encode($model->title) ?>"/>
                                        </li>
                                    <? endif;?>
                                    <?php foreach ($model->images as $image): ?>
                                         <? if($image->getThumbFileUrl('image', 'ico', '/img/no_image.jpg') && $image->getImageFileUrl('image') &&  $image->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg')):?>
       
                                        <li data-thumb="<?= $image->getThumbFileUrl('image', 'ico', '/img/no_image.jpg') ?>"
                                            data-src="<?= $image->getImageFileUrl('image') ?>">
                                            <img src="<?= $image->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg') ?>"
                                                 alt="<?= Html::encode($image->alt) ?>"/>
                                        </li>
                                        <? endif;?>
                                    <? endforeach; ?>
                                        
                             
                          </ul> 
                        </div>
                      
                        <? if(!empty($model->size) && count($model->size)>1):?>
                            <? include \Yii::$app->modules["shop"]->viewPath."/product/color_block.php";?>
                        <? endif;?>
                    </div>
                    
                    <div class="col-md-6" >  
                        
                        <div class="col-xs-12">
                            <div class="row">
                                <? if(isset($skus) && is_array($skus) && !empty($skus)):?>
                                    <div  >  
                                        <? if(empty($model->size)):?>
                                            <? include \Yii::$app->modules["shop"]->viewPath."/product/color_block.php";?>
                                        <? endif;?>
                                    </div>
                                 
                                <? endif;?>            
                                <div  id="ajax-body" >      
                                    
                                    <? include \Yii::$app->modules["shop"]->viewPath."/product/product_body.php";?>
                                </div>     
                            </div>
                                
                            <div class="desc" id="ajax-desc">  
                                <?= $model->description ?>
                            </div>
                        </div>
                        <?if(!empty($model->option)):?>
                            <div id="ajax-option">
                                 <? include \Yii::$app->modules["shop"]->viewPath."/product/option_block.php";?>
                            </div>    
                        <?endif;?>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

