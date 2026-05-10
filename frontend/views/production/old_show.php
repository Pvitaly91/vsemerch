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
            <h1><?= $text->title; ?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        ['label' => Yii::t('app', 'Production'), 'url' => ['production/index']],
                        $text->title],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container cnt">

                <div class="row">
                    <div class="col-md-4 text-center">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Avto-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Avto-500x500.jpg" width="400" height="400" class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                    <div class="col-md-4 text-center">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Naruzhka-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Naruzhka-500x500.jpg" width="400" height="400" class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                    <div class="col-md-4 text-center">
                        <a href="<?=Yii::$app->request->baseUrl?>/img/Set_new_Promo-500x500.jpg" rel="shadowbox[gal]" class="fancybox" title="Внешняя реклама">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/Set_new_Promo-500x500.jpg" width="400" height="400" class="img-responsive" border="0" alt="Внешняя реклама" />
                        </a>
                    </div>
                </div>
                <hr />
            <?= $text->body; ?>


        </div>
    </div>
</div>


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
<?/**
<div class="box3">
<div class="container">
    <div class="text-center"><h3>НАШИ РАБОТЫ</h3></div>
    <div class="slider1">
                        <div class="slide">

<div class="view4">
    <a href="/img/bord/bigboard.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/bigboard.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/bigboard.jpg"> 1</a>
    </div>


</div>
            </div>
                        <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard1.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard1.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard1.jpg"> 2</a>
    </div>


</div>
            </div>
                        <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard2.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard2.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard2.jpg"> 3</a>
    </div>


</div>
            </div>
                        <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard3.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard3.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard3.jpg">4</a>
    </div>


</div>
            </div>
            <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard4.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard4.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard4.jpg">5</a>
    </div>


</div>
            </div>
            <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard5.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard5.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard5.jpg">6</a>
    </div>


</div>
            </div>
            <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard6.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard6.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard6.jpg">7</a>
    </div>


</div>
            </div>
            <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard7.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard7.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard7.jpg">8</a>
    </div>


</div>
            </div>
                        <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard8.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard8.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard8.jpg">9</a>
    </div>


</div>
            </div>
             <div class="slide">

<div class="view4">
    <a href="/img/bord/billboard9.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/bord/billboard9.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/bord/billboard9.jpg">10</a>
    </div>


</div>
            </div>


    </div>
</div>
 **/?>