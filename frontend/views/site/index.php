<?php

use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use common\components\Text;
use frontend\models\Slider;
use frontend\models\Catalog;
use frontend\models\Option;
use frontend\models\Products;
use frontend\models\Disease;
use frontend\models\Partners;

/* @var $this yii\web\View */

$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

/*
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
 */
$this->registerCssFile('/css/main.css');
$this->registerCssFile(Yii::$app->request->BaseUrl . '/js/fancyBox/source/jquery.fancybox.css?v=2.1.5');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/fancyBox/source/jquery.fancybox.js?v=2.1.5', ['position' => View::POS_HEAD, 'depends' => ['yii\web\JqueryAsset']]);
$this->registerJs("
$('.fancybox').fancybox();

", View::POS_READY, 'fancybox');

$this->registerJs("
$('.buttonMall').addClass('open');
", View::POS_READY, 'openMenu');
?>
<div class="header-main">
    <? /**
      <div id="video-bg">
      <video width="100%" height="auto" autoplay loop muted playsinline preload="yes">
      <source src="/img/video/AGcity_2.mp4" type="video/mp4"></source>
      <source src="/img/video/AGcity_2.webm" type="video/webm"></source>
      </video>
      </div>

      <div style="position: relative;z-index: 3;" class="container">

      <!-- <div class="scott hidden-xs"></div> -->
      <div class="info">

      <div><h4><?=Yii::t('app', 'У Вас есть задача?')?></h4><br><h3><?=Yii::t('app', 'У нас есть решение!')?></h3></div>
      <div></div>
      <a href="#" class="btn1" data-toggle="modal" data-target="#modal_mail" style="margin-top:20px; margin-left: 1.6em;"><?=Yii::t('app', 'Связатся с нами')?> </a>
      </div>
      </div>
     * */ ?>

    <div id="carousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel" data-slide-to="0" class="active"></li>
            <li data-target="#carousel" data-slide-to="1"></li>
            <li data-target="#carousel" data-slide-to="2"></li>
            <li data-target="#carousel" data-slide-to="3"></li>
            <li data-target="#carousel" data-slide-to="4"></li>
        </ol>

        <div class="carousel-inner">
            <div class="item active">
                <img src="/img/carousel/1.jpg" alt="">
            </div>
            <div class="item">
                <img src="/img/carousel/2.jpg" alt="">
            </div>
            <div class="item">
                <img src="/img/carousel/3.jpg" alt="">
            </div>
            <div class="item">
                <img src="/img/carousel/4.jpg" alt="">
            </div>
            <div class="item">
                <img src="/img/carousel/5.jpg" alt="">
            </div>
        </div>
        <!-- Controls -->
        <a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>

</div>

<div class="about_box">
    <div class="top3"></div>
    <!-- <div class="top"></div> -->
    <div class="content" style="background-image: url(/img/disignweb/bg-1.png);background-size: auto;">

        <div class="container">
            <div class="h1begin"><h1><?= Yii::t('app', 'Сувенирная продукция с логотипом - брендирование продукции') ?></h1></div>

            <div class="designline2" style="margin-top: 0px">
                <div class="designline" style="margin-top: 0px">
                    <div class="mainh1"><?= Yii::t('app', 'ДИЗАЙН') ?></div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-4 text-center">
                    <div class="xzm4 cropp">
                        <a href="<?= Url::to(['design/show', 'slug' => 'web-design']) ?>">
                            <!-- <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/1.jpg" width="310px" height="212px" border="0" /> -->
                            <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/1.png" width="100%" height="100%" border="0" />
                        </a>
                    </div>
                    <div class="xzm3" style="background-image: url(/img/disignweb/2.png);background-size: cover;width="100%"">
                        <br>
                        <div class="mainh2"><?= Yii::t('app', 'ДИЗАЙН САЙТОВ') ?></div>
                        <div class="linetext"></div>
                        <div class="mainh3"><?= Yii::t('app', '(UI/UX DESIGN)') ?></div>
                        <!--<br>-->
                        <br>
                        <br>
                        <div class="mainp1"><?= Yii::t('app', 'Сайт — это лицо компании и одна из важнейших сос&shy;тавляющих ее успешности') ?></div>
                        <br>
                        <a href="<?= Url::to(['design/show', 'slug' => 'web-design']) ?>" class="btn2"><?= Yii::t('app', 'Подробнее') ?></a>
                    </div>
                </div>

                <div class="col-md-4 text-center">
                    <div class="xzm4 cropp">
                        <a href="<?= Url::to(['design/show', 'slug' => 'dizayn_poligrafii_katalogi_buklety_prezentacii']) ?>">
                            <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/3.png" width="100%" height="100%" border="0" />
                        </a>
                    </div>
                    <div class="xzm3" style="background-image: url(/img/disignweb/4.png); background-size: cover;">
                        <br>
                        <div class="mainh2"><?= Yii::t('app', 'ДИЗАЙН ПОЛИГРАФИИ') ?></div>
                        <div class="linetext"></div>
                        <div class="mainh3"><?= Yii::t('app', '(КАТАЛОГИ, БУКЛЕТЫ, ПРЕЗЕНТАЦИИ)') ?></div>
                        <br>

                        <div class="mainp1"><?= Yii::t('app', 'Дизайн полиграфии — это разработка графического дизайна под печатную продукцию.') ?></div>
                        <br>
                        <a href="<?= Url::to(['design/show', 'slug' => 'dizayn_poligrafii_katalogi_buklety_prezentacii']) ?>" class="btn2"><?= Yii::t('app', 'Подробнее') ?></a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="xzm4 cropp">
                        <a href="<?= Url::to(['design/show', 'slug' => 'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka']) ?>">
                            <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/5.png" width="100%" height="100%" border="0" />
                        </a>
                    </div>
                    <div class="xzm3" style="background-image: url(/img/disignweb/6.png);background-size: cover;">
                        <br>
                        <div class="mainh2"><?= Yii::t('app', 'ДИЗАЙН ПОД КЛЮЧ') ?></div>
                        <div class="linetext"></div>
                        <div class="mainh3"><?= Yii::t('app', '(ЛОГОТИПА, ФИРМЕННОГО СТИЛЯ, СЛОГАНА, БРЕНДБУКА)') ?></div>
                        <br>
                        <div class="mainp1"><?= Yii::t('app', 'Логотип и фирменный стиль — это визуальный образ компании, то что делает вас уникальными и запоминающимися') ?></div>
                        <!-- <br> -->
                        <a href="<?= Url::to(['design/show', 'slug' => 'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka']) ?>" class="btn2"><?= Yii::t('app', 'Подробнее') ?></a>

                    </div>
                </div>
            </div>
        </div>


        <!-- <div class="botton2"></div> -->
    </div>
    <div class="content" style="background-image: url(/img/disignweb/bg-2.png);background-size: cover;">
        <div class="container">
            <div class="designline2" style="margin-top: 40px">
                <div class="designline4" style="margin-top: 40px">
                    <div class="mainh1"><?= Yii::t('app', 'ПОЛИГРАФИЧЕСКАЯ ПРОДУКЦИЯ') ?></div>
                </div>
            </div>


            <div class="row pr">
                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'vizitki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/visitki.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'vizitki']) ?>" class="btn3">
                                <?= Yii::t('app', 'ВИЗИТКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>

                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'katalogi']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/catalog.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'katalogi']) ?>" class="btn3"><?= Yii::t('app', 'КАТАЛОГИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'listovki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/листовка.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'listovki']) ?>" class="btn3"><?= Yii::t('app', 'ЛИСТОВКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'flaery']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/флаера.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'flaery']) ?>" class="btn3"><?= Yii::t('app', 'ФЛАЕРА') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'birki_i_cenniki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/бирки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'birki_i_cenniki']) ?>" class="btn3"><?= Yii::t('app', 'БИРКИ И ЦЕННИКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'plakaty']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/плакаты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'plakaty']) ?>" class="btn3"><?= Yii::t('app', 'ПЛАКАТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'korobki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/коробки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'korobki']) ?>" class="btn3"><?= Yii::t('app', 'КОРОБКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'nakleyki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/наклейки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'nakleyki']) ?>" class="btn3"><?= Yii::t('app', 'НАКЛЕЙКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'papki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/папки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'papki']) ?>" class="btn3"><?= Yii::t('app', 'ПАПКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'menyu']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/меню.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'menyu']) ?>" class="btn3"><?= Yii::t('app', 'МЕНЮ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'otkrytki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/открытки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'otkrytki']) ?>" class="btn3"><?= Yii::t('app', 'ОТКРЫТКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'plastik']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/карты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'plastik']) ?>" class="btn3"><?= Yii::t('app', 'ПЛАСТИКОВЫЕ КАРТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'konverty']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/конверты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'konverty']) ?>" class="btn3"><?= Yii::t('app', 'КОНВЕРТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'kalendari']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/календари.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'kalendari']) ?>" class="btn3"><?= Yii::t('app', 'КАЛЕНДАРИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>

                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'buklety']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/буклеты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'buklety']) ?>" class="btn3"><?= Yii::t('app', 'БУКЛЕТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'broshyury']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/брошюры.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'broshyury']) ?>" class="btn3"><?= Yii::t('app', 'БРОШЮРЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'blanki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/бланки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'blanki']) ?>" class="btn3"><?= Yii::t('app', 'БЛАНКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'bloknoty']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/блокноты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'bloknoty']) ?>" class="btn3"><?= Yii::t('app', 'БЛОКНОТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'pakety']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/пакеты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'pakety']) ?>" class="btn3"><?= Yii::t('app', 'ПАКЕТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'bumazhnye_stakany']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/стаканчики.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'bumazhnye_stakany']) ?>" class="btn3"><?= Yii::t('app', 'СТАКАНЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content" style="background-image: url(/img/disignweb/bg-3.png);background-size: cover;">

        <div class="container">

            <div class="designline2" style="margin-top: 20px">
                <div class="designline3" style="margin-top: 20px">
                    <div class="mainh1"><?= Yii::t('app', 'СУВЕНИРНАЯ ПРОДУКЦИЯ') ?></div>
                </div>
            </div>
            <div class="row pr">

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'salfetki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/салфетки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'salfetki']) ?>" class="btn3"><?= Yii::t('app', 'САЛФЕТКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'spichki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/спички.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'spichki']) ?>" class="btn3"><?= Yii::t('app', 'СПИЧКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'ruchki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/ручки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'ruchki']) ?>" class="btn3"><?= Yii::t('app', 'РУЧКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'futbolki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/футболки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'futbolki']) ?>" class="btn3"><?= Yii::t('app', 'ФУТБОЛКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'kepki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/кепки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'kepki']) ?>" class="btn3"><?= Yii::t('app', 'КЕПКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>

                    </div>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'zonty']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/зонты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'zonty']) ?>" class="btn3"><?= Yii::t('app', 'ЗОНТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'magnity']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/магниты.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'magnity']) ?>" class="btn3"><?= Yii::t('app', 'МАГНИТЫ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-2">
                    <div class="xzm cropp">
                        <div>
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'chashki']) ?>" rel="shadowbox[gal]" class="fancybox">
                                <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/чашки.png" width="170px" height="129px" border="0" />
                        </div>
                        <div class="xzm2">
                            <a href="<?= Url::to(['catalog/show', 'translit' => 'chashki']) ?>" class="btn3"><?= Yii::t('app', 'ЧАШКИ') ?><br>
<?= Yii::t('app', 'Дизайн и печать') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content" style="background-image: url(/img/disignweb/bg-4.png);background-size: cover;">
        <div class="container">
            <div class="designline2" style="margin-top: 20px">
                <div class="designline6" style="margin-top: 20px">
                    <div class="mainh1"><?= Yii::t('app', 'НАРУЖНАЯ РЕКЛАМА И ШИРОКОФОРМАТНАЯ ПЕЧАТЬ') ?></div>
                </div>
            </div>
            <div class="row pr">

                <div class="col-xs-12 col-sm-2">
                    <div class="q7">
                        <div>
                            <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/reclam-nar.png"  width=100%; border="0" />
                            <a href="<?= Url::to(['production/show', 'slug' => 'naruzhnaya_reklama']) ?>" class="btn5" style=" position: absolute; left: 15px; top: 281px"><?= Yii::t('app', 'ШИРОКОФОРМАТНАЯ ПЕЧАТЬ,ПРОИЗВОДСТВО РЕКЛАМНОЙ КОНСТРУКЦИИ
                        </br>,БРЕНДИРОВАНИЕ ТРАНСПОРТА') ?></a>
                        </div>
                        <div class="q8">
                            <a href="<?= Url::to(['production/show', 'slug' => 'naruzhnaya_reklama']) ?>" class="btn4"><?= Yii::t('app', 'НАРУЖНАЯ РЕКЛАМА') ?><br>
<?= Yii::t('app', 'дизайн, печать,производсво') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="content" style="background-image: url(/img/disignweb/bg-5.png);background-size: cover;">
        <div class="container">
            <div class="designline2" style="margin-top: 20px">
                <div class="designline3" style="margin-top: 20px">
                    <div class="mainh1"><?= Yii::t('app', 'НОВОСТИ И АКЦИИ') ?></div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
<? foreach ($news as $item): ?>
                    <div class="col-md-4 text-center">
                        <div class="xzm4 cropp">
                            <a href="<?= Url::to(['news/show', 'translit' => $item->translit, 'id' => $item->id]) ?>">
                                <img src="<?= Yii::$app->request->baseUrl . '/upload/news/ico/' . $item->image ?>" width="100%" height="100%" border="0" />
                            </a>
                        </div>
                        <div class="xzm3" style="background-image: url(<?= Yii::$app->request->baseUrl . '/upload/news/ico/' . $item->imageb ?>);background-size: cover;">
                            <br>
                            <div class="mainh2"><?= $item->title ?></div>
                            <div class="linetext"></div>
                            <div class="mainh3"><?= $item->text ?></div>
                            <br>
                            <div class="mainp1"><?= $item->meta_description ?></div>
                            <!-- <br> -->
                            <a href="<?= Url::to(['news/show', 'translit' => $item->translit, 'id' => $item->id]) ?>" class="btn2"><?= Yii::t('app', 'Подробнее') ?></a>
                        </div>
                    </div>
<? endforeach; ?>
            </div>
        </div>
    </div>


    <div class="content" style="background-color:#fff ">
        <div class="container">

            <div class="row pr">

                <div class="col-xs-12 col-sm-2">
                    <div class="q7">
                        <div>
<?= Yii::t('app', 'Закажите обратный звонок чтобы узнать подробнее об интересующей Вас информации!') ?>
                        </div>
                        <div class="q10">
                            <a href="#" class="btn1" data-toggle="modal" data-target="#modal_mail" style="margin-top:20px"><?= Yii::t('app', 'Перезвоните мне!') ?> </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content" style="background-image: url(/img/disignweb/bg-5.png);background-size: cover;">
    <div class="container">
<?= $textSeo->body ?>
    </div>
</div>

<?
yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader', 'class' => 'text-center'],
    'header' => '<h2>' . Yii::t('app', 'Mail send') . '</h2>',
    'id' => 'modal_mail',
]);

$modelPopup = new \frontend\models\Mail();
echo $this->context->renderPartial('/mail/index', [
    'model' => $modelPopup,
]);
yii\bootstrap\Modal::end();
?>

