<?php
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;

$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/jquery.bxslider/jquery.bxslider.css', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.bxslider/jquery.bxslider.min.js',['position'=>View::POS_END,'depends'=>['yii\web\JqueryAsset']]);
$this->registerJs("
  $('.slider1').bxSlider({
    slideWidth: 315,
    minSlides: 1,
    maxSlides: 3,
    slideMargin: 10
  });

", View::POS_READY, 'bxslider');

$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/fancyBox/source/jquery.fancybox.css?v=2.1.5', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/fancyBox/source/jquery.fancybox.js?v=2.1.5',['position'=>View::POS_END,'depends'=>['yii\web\JqueryAsset']]);
$this->registerJs("
$('.fancybox').fancybox();

", View::POS_READY, 'fancybox');
?>


<div class="head2">
        <div class="container">
                <div class="info2 col-md-12">
                <h1><?=$text->title;?></h1>


                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],
                                    'links' => [$text->title],
                                ]) ?>
                        </nav>
                </div>
        </div>
</div>
<div class="body_box">
<div class="top2"></div>
<div class="content">
<div class="container cnt"><div class="col-md-12">



<?=$text->body;?>
    </div>      </div></div></div>

<?if(!empty($text->fotos)):?>
    <div class="box3">
        <div class="container">
            <div class="text-center"><h3><?=Yii::t('app', 'OUR WORK')?></h3></div>
            <div class="slider1">
                <?foreach($text->fotos as $item):?>
                    <div class="slide">
                        <div class="view4">
                            <a href="<?=Yii::$app->request->baseUrl.'/upload/text_fotos/'.$item->image?>" rel="shadowbox[gal1]" class="fancybox" title="<?=Html::encode($item->name)?>">
                                <img src="<?=Yii::$app->request->baseUrl.'/upload/text_fotos/ico/'.$item->image?>" alt="<?=Html::encode($item->name)?>" width="255" height="170" border="0" />
                            </a>
                            <?/**
                            <div class="mask">
                                <a rel="shadowbox[gal]" class="fancybox" href="<?=Yii::$app->request->baseUrl.'/upload/text_fotos/'.$item->image?>"><?=$item->name?></a>
                            </div>**/?>
                        </div>
                    </div>
                <?endforeach;?>


            </div>
        </div>
    </div>
<?endif;?>

