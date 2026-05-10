<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use frontend\assets\AppAsset;
use common\components\LangWidget;
use common\components\HreflangWidget;
use frontend\models\Option;
use kartik\alert\AlertBlock;
use lo\widgets\modal\ModalAjax;

$itemsInCart = Yii::$app->cart->getCount();

AppAsset::register($this);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/html5.js', ['position' => View::POS_HEAD, 'condition' => 'lt IE 9']);
$this->registerJsFile(Yii::$app->request->BaseUrl . '/js/respond.js', ['position' => View::POS_HEAD, 'condition' => 'lt IE 9']);
$this->registerMetaTag(['name' => 'X-UA-Compatible', 'content' => 'IE=Edge']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?/*<meta property="og:image" content="<?= (!empty($this->params['og:image'])) ? $this->params['og:image'] : Url::to('img/logo.png', true) ?>" />*/?>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= HreflangWidget::widget([]) ?>
    <?php $this->head() ?>
    <? if(IS_PROD == true):?>
            <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10941815611"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'AW-10941815611');
        </script>
    
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-KM2HBDV');</script>
        <!-- End Google Tag Manager -->

    <? endif;?>
  
    <? if(  IS_LOCAL == true):?>  
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-78HSYEM2N0"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'G-78HSYEM2N0');
        </script>
   <? endif;?>
    <style>
        @media only screen and (max-width: 767px) {
            .hide-767{
                display: none;
            }

            .nav-margin{
                margin-top: 50px;
            }
            .header-bar{
                position: absolute;
                top:30px;
                right: 60px;
                height: 50px;

            }
            .basket {
                color: #ffffff;
                font-size: 13px;
                font-weight: bold;
                font-family: OpenSansBold;
                background-color: rgba(0, 0, 0, 0.3);
                /* padding: 7px 7px 5px 7px; */
                border-radius: 50%;
                z-index: 999999 !important;
                width: 50px;
                height: 50px;
            }
            .basket .ico {
                position: relative;
                width: 40px;
                height: 40px;
                margin: -7px;
            }
            .basket .ico span {
                position: absolute;
                right: -8px;
                top: -5px;
                background: #03a084;
                padding: 3px 8px;
                border-radius: 50%;
                color: #ffffff;
            }
            .header-bar a{
                margin-right: 10px;
            }
            .copyright-info{
                width: 100% !important;
                margin-bottom: 20px;
            }
        }
        @media only screen and (min-width: 767px) {

            .show-767{
                display: none ;
            }

        }
    </style>

    <?/** <script>
    (function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function() {
    (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
    m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-103091832-1', 'auto');
    ga('send', 'pageview');
    </script> **/?>

    <?/**
    <script type="text/javascript" src="https://www.l2.io/ip.js?var=userip"></script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
    (function(d, w, c) {
    (w[c] = w[c] || []).push(function() {
    try {
    w.yaCounter45407004 = new Ya.Metrika({
    id: 45407004,
    clickmap: true,
    trackLinks: true,
    accurateTrackBounce: true,
    webvisor: true,
    params: {
    'ip': userip
    }
    });
    } catch (e) {}
    });
    var n = d.getElementsByTagName("script")[0],
    s = d.createElement("script"),
    f = function() {
    n.parentNode.insertBefore(s, n);
    };
    s.type = "text/javascript";
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/watch.js";
    if (w.opera == "[object Opera]") {
    d.addEventListener("DOMContentLoaded", f, false);
    } else {
    f();
    }
    })(document, window, "yandex_metrika_callbacks");
    </script> <noscript>
    <div><img src="https://mc.yandex.ru/watch/45407004" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript> <!-- /Yandex.Metrika counter -->
     **/?>
    <?/**
    <!--Start of UniSender PopUp Form script-->
    <script type="text/javascript" src="//popup-static.unisender.com/service/loader.js?c=15413" id="unisender-popup-forms"></script>
    <!--End of UniSender PopUp Form script-->
     **/?>
    <style>
        .header-contacts {
            margin-top: -32px;
            left: 122px;
            position: relative;
            z-index:990;
            float: right !important;
        }
        @media (max-width: 768px) {
            .navbar-header {
                min-height: 60px;
                height: 100%;
            }
            /* .navbar-default {
                background-color: #e7e8e6!important;
            } */
            .navbar-default .langs {
                margin: 0;
                margin-bottom: 15px;
                height: 20px;
                display: block!important;
            }
            .navbar-default .langs ul li a,
            .navbar-default .langs ul li a:visited,
            .navbar-default .langs ul li a:link {
                color: #000;
            }
            .navbar-default .langs ul li a.active {
                color: #e81c25;
            }
            .navbar-default .navbar-collapse .header-contacts {
                margin-top: 0;
                left: auto;
                float: none !important;
            }
            .navbar-default .navbar-collapse .header-contacts > span {
                display: block;
                margin-top: 15px;
            }
            .footer-socials-wrapper {
                width: 100%;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .footer-socials-wrapper > span {
                width: 100%;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                column-gap: 5px;
            }
            .footer-socials-wrapper .footer-socials {
                width: 100%;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                column-gap: 10px;
            }
        }
        .basket a.link {
           
            font-size: 14px;
        }
        .basket .ico span {
            padding: 1px 6px;
            right: 58px;
            top: -5px;
        }

        @media only screen and (max-width: 991px) {
            #block-nav {
                position: relative;
            }

            #block-nav-1 {
                top :80px;
                position: absolute;
            }

            #block-nav-2 {
                top :10px;
                position: absolute;
            }
            .basket-block{
                margin-top: 150px;
            }
            #top{
                height: 300px;
            }
            .head2 {
                height: 480px;
            }
            .head2 .container .info2 {
                top:300px;
            }
            .head2 .container .info2 h1{
                margin-top: 30px;
            }
        }
    </style>
   
</head>

<body>
<? if(IS_PROD == true):?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KM2HBDV"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<? endif;?>
<?php $this->beginBody() ?>

<? /**
<div id="loading-screen">
<div class="sk-fading-circle">
<div class="sk-circle1 sk-circle"></div>
<div class="sk-circle2 sk-circle"></div>
<div class="sk-circle3 sk-circle"></div>
<div class="sk-circle4 sk-circle"></div>
<div class="sk-circle5 sk-circle"></div>
<div class="sk-circle6 sk-circle"></div>
<div class="sk-circle7 sk-circle"></div>
<div class="sk-circle8 sk-circle"></div>
<div class="sk-circle9 sk-circle"></div>
<div class="sk-circle10 sk-circle"></div>
<div class="sk-circle11 sk-circle"></div>
<div class="sk-circle12 sk-circle"></div>
</div>
</div>
 **/ ?>
<!-- MAIN HEADER -->

<header class="main-header" id="main-header">
    <nav id="top" class="navbar navbar-default navbar-fixed-top navbar-3">
        <div class="container" style="position: relative;">
            <div class="show-767 header-bar"  style="z-index: 99">
                <a href="viber://add?number=380635959213"><img src="/img/viber_ico.png" width="51" height="51" /></a>
                <a title="Instagram" target="_blank" href="<?= Option::show('Instagram_link') ?>"><img src="/img/instagram-r-41.png" width="51" height="51" /></a>
                <a title="Facebook" target="_blank" href="<?= Option::show('FaceBook_link') ?>"><img src="/img/facebook-icon-41.png" width="51" height="51" /></a>

                <? if (Yii::$app->controller->id != 'cart' && Yii::$app->controller->action->id != 'order') : ?>

                    <div  class="basket " style="display: inline-block">

                        <div class="ico">
                            <span><?= $itemsInCart ?></span>
                            <a href="#">
                                <img src="/img/basket.png" width="51" height="51" border="0" />

                            </a>
                        </div>


                    </div>
                <? endif; ?>

            </div>
            <div class="navbar-header clearfix nav-margin" >

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#redone-navbar" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>




                <!-- NAVBAR BRAND -->
                <a class="navbar-brand" href="<?= Url::to(['/site/index']) ?>"><img src="<?= Yii::$app->request->BaseUrl ?>/img/logo.png" width="190" height="82" alt="AG CITY"></a>

            </div> <!-- .navbar-header ends -->


            <div class="collapse navbar-collapse bg-menu" id="redone-navbar">
                <div class="langs pull-right hidden-xs">
                    <?= LangWidget::widget(['active' => Yii::$app->language]) ?>
                </div>
                <div class="header-contacts " >
                    <a href="viber://add?number=380635959213" class="hide-767"><img src="/img/viber_ico.png" width="20" height="20" /></a>
                    <a title="Instagram" class="hide-767 target="_blank" href="<?= Option::show('Instagram_link') ?>"><img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/instagram.png" width="20" height="20" /></a>

                    <a title="Facebook" class="hide-767 target="_blank" href="<?= Option::show('FaceBook_link') ?>"><img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/facebook.png" width="20" height="20" /></a>

                    <span>
                            <img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/mail.png" width="20" height="20" />
                            <span style="font-family: OpenSansBold;"><?= Yii::t('app', '  ') ?>
                                <a style="color:#000" href="mail:<?= Option::show('email_top') ?>"><?= Option::show('email_top') ?></a>
                            </span>
                        </span>

                </div>

                <ul class="nav navbar-nav navbar-right dector ">
                    <li><a href="<?= Url::to(['/site/index']) ?>"><?= Yii::t('app', 'HOME') ?></a></li>
                    <li><a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>"><?= Yii::t('app', 'About') ?></a></li>
                    <li><a href="<?= Url::to(['/design/index']) ?>"><?= Yii::t('app', 'Design') ?></a></li>
                    <li><a href="<?= Url::to(['/portfolio']) ?>"><?= Yii::t('app', 'Portfolio') ?></a></li>
                    <li><a href="<?= Url::to(['/blog']) ?>"><?= Yii::t('app', 'Blog') ?></a></li>
                    <li><a href="<?= Url::to(['/contacts/index']) ?>"><?= Yii::t('app', 'CONTACTS') ?></a></li>

                </ul> <!-- .nav navbar-nav ends -->

            </div> <!-- .collapse navbar-collapse ends -->
            <div class="both row " >

                <div id="block-nav">
                <div class="col-md-3" id="block-nav-1">
                    <div class="mallbox"><?= \frontend\widgets\CategoryWidget::widget(); ?></div>
                </div>
                <div class="col-md-5 " id="block-nav-2">
                    <div class="search_box">
                        <form method="get" action="<?= Url::to(['/shop/catalog/search']) ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_str" required oninvalid="this.setCustomValidity('Заполните поле для поиска')" onkeyup="lookup(this.value);" autocomplete="off" value="<?= Html::encode(Yii::$app->request->get('search_str')) ?>" placeholder="<?= Yii::t('shop', 'Product Search') ?>">
                                <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                    </span>
                            </div>
                            <div id="searchRes">Загрузка...</div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="col-md-4 hide-767" style="margin-top: 7px;">
                    <? if (Yii::$app->controller->id != 'cart' && Yii::$app->controller->action->id != 'order') : ?>
                        <div  class="basket row basket-block">
                            <div class="col-md-3" style="margin-top: 3px" >
                                <div class="ico">
                                    <span><?= $itemsInCart ?></span>
                                    <a href="#">
                                        <img src="/img/basket.png" width="40" height="40" border="0" />
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <a href="#" class="link"><?= Yii::t('shop', 'Cart') ?></a>
                                <?= Yii::t('shop', 'In the basket {n} goods', ['n' => "<span>$itemsInCart</span>"]) ?>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>

        </div> <!-- .container ends -->



    </nav> <!-- .navbar navbar-default ends -->
</header> <!-- .main-header ends -->


<div class="top"></div>

<?= $content ?>


<div class="bottom-bar">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 copyright-info " style="color:#fff; text-align: center;">
                <img src="<?= Yii::$app->request->BaseUrl ?>/img/disignweb/logo.png" width="139" height="60" alt="AG CITY">
                <br>
                Copyright @ <?=date("Y")?>, agcity.com.ua
            </div> <!-- .col-sm-6 ends -->
            <div class="col-md-5 text-center" style="">
                <ul class="nav navbar-nav navbar-right dector" style="margin-top: 1px;">

                    <li><a href="<?= Url::to(['/news/index']) ?>" style="color:#fff"><?= Yii::t('app', 'NEWS') ?></a></li>
                    <li><a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>" style="color:#fff"><?= Yii::t('app', 'About') ?></a></li>
                    <li><a href="<?= Url::to(['/design/index']) ?>" style="color:#fff"><?= Yii::t('app', 'Design') ?></a></li>
                    <?/* <li><a href="<?= Url::to(['/web/index']) ?>" style="color:#fff"><?= Yii::t('app', 'Web') ?></a></li>*/?>
                    <li><a href="<?= Url::to(['/production/index']) ?>" style="color:#fff"><?= Yii::t('app', 'Production') ?></a></li>
                    <li><a href="<?= Url::to(['/contacts/index']) ?>" style="color:#fff"><?= Yii::t('app', 'CONTACTS') ?></a></li>
                    <li><a href="<?= Url::to(['/text/index', 'slug' => 'obmen_i_vozvrat_tovara']) ?>" style="color:#fff"><?= Yii::t('app', 'Exchange and return') ?></a></li>
                    <li><a href="<?= Url::to(['/text/index', 'slug' => 'oplata_i_dostavka_tovara']) ?>" style="color:#fff"><?= Yii::t('app', 'Payment and delivery') ?></a></li>
                </ul> <!-- .nav navbar-nav ends -->
            </div> <!-- .col-sm-6 ends -->
            <div class="col-sm-3 text-right footer-socials-wrapper" style="">

                <div class="soc footer-socials">
                    <a title="Facabook" target="_blank" href="<?= Option::show('FaceBook_link') ?>"><img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/face.png" width="22" height="22" /></a>
                    <a title="Instagram" target="_blank" href="<?= Option::show('Instagram_link') ?>"><img src="<?= Yii::$app->request->baseUrl ?>/img/disignweb/instagram.png" width="20" height="20" /></a>
                </div>
            </div> <!-- .col-sm-6 ends -->
        </div>
    </div>
</div>

<?php
echo AlertBlock::widget([
    'type' => AlertBlock::TYPE_GROWL,
    'useSessionFlash' => true
]);

echo ModalAjax::widget([
    'id' => 'modalCart',
    'size' => 'modal-lg',
    'header' => '<div class="h2">' . Yii::t('shop', 'Cart') . '</div>',
    'url' => Url::to(['/shop/cart/list']), // Ajax view with form to load
    //'ajaxSubmit' => true, // Submit the contained form as ajax, true by default
]);
?>

<?/*<div id="viber">
        <a href="viber://add?number=380635959213"><img src="/img/viber_ico.png" /></a>
    </div>*/?>
<?
//<script>var telerWdWidgetId="9ba35231-b447-4fb6-a76c-dc90b4c301e2";var telerWdDomain="free-cc.phonet.com.ua";</script> <script src="//free-cc.phonet.com.ua/public/widget/call-catcher/lib-v3.js"></script>
?>

<?php $this->endBody() ?>
<script>
$(document).ready(function () {

   

   // $('.add_to_cart').click(function () {
    $(document).on('click', ".add_to_cart", function () {   
        if($(this).attr('disabled')) {
            return false;
        }
        var url = $(this).attr('href');
        var size_id = $(this).data('size_id');
        if(size_id) {
            url += '&size_id=' + size_id;
        }
        $.get(url, function (data) {
            reloadCart();
            $('#modalCart').modal({'show': true});
        });
        return false;
    });

    $('.basket').click(function () {
       
        $('#modalCart').modal({'show': true});
        return false;
    });

    $(document).on('pjax:success', function() {
      reloadCart();
    });

    var reloadCart = function () {
        $.get('/shop/cart/items-in-cart', function (data) {
            $('.basket').find('span').html(data);
        });
    }
    //$('.size-box').click(function () {
     $(document).on('click', ".size-box", function () {
        
        //alert();
        var price = $(this).data('price');
        var id = $(this).data('id');
        var code = $(this).data('code');
        var not_available = $(this).data('not_available');
        if(not_available == 1) {
            $('.available-text').removeClass('are_available').addClass('not_available');
            $('.available-text').text('<?=Yii::t('shop', 'Not available')?>');
            $('.add_to_cart').attr('disabled', true);
        } else {
            $('.available-text').removeClass('not_available').addClass('are_available');
            $('.available-text').text('<?=Yii::t('shop', 'Are available')?>');
            $('.add_to_cart').attr('disabled', false);
        }
        $('.old-price-text').remove();
        $('.size-box').removeClass('active-size');
        $(this).addClass('active-size');
        $('.price-text').removeClass('hidden');
        $('.price-text').children('span').text(price + ' ₴');
        $('.code-text').children('span').text(code);
        $('.add_to_cart').data('size_id', id);
        return false;
    });
});
</script>

</body>

</html>
<?php $this->endPage() ?>
