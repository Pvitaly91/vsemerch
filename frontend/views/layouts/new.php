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
use dmstr\widgets\Alert;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?/*<meta property="og:image" content="<?= (!empty($this->params['og:image'])) ? $this->params['og:image'] : Url::to('img/logo.png', true) ?>" />*/?>
        <?= Html::csrfMetaTags() ?>
         <link rel="stylesheet" href="/new/css/swiper-bundle.min.css" />
        <link rel="stylesheet" href="/new/js/slider/_slider.css" />
       
        <link rel="stylesheet" href="/new/css/UI/header.css" />
        <link rel="stylesheet" href="/new/css/UI/footer.css" />
       
        <? if((
                property_exists(\Yii::$app->controller->module, "controller")
                && get_class(\Yii::$app->controller->module->controller) != "frontend\\controllers\\SiteController"
                && get_class(\Yii::$app->controller->module) != "frontend\\modules\\shop\\Module"
                )
                || Yii::$app->response->statusCode != 200
                ): ?>
            <link rel="stylesheet" href="/new/css/product-card-3.css" />
 
        <? endif;?>
        <link rel="stylesheet" href="/new/css/modal.css" />
        
       
        <title><?= Html::encode($this->title) ?></title>
        <?= HreflangWidget::widget([]) ?>
        <?php $this->head() ?>
         <link rel="stylesheet" href="/new/css/fix.css" />
         
        <? if (IS_PROD == true): ?>
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10941815611"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());

                gtag('config', 'AW-10941815611');
            </script>

            <!-- Google Tag Manager -->
            <script>(function (w, d, s, l, i) {
                    w[l] = w[l] || [];w[l].push({'gtm.start':
                                new Date().getTime(), event: 'gtm.js'});
                    var f = d.getElementsByTagName(s)[0],
                            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', 'GTM-KM2HBDV');</script>
            <!-- End Google Tag Manager -->

<? endif; ?>

        <? if (IS_LOCAL == true): ?>  
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
<? endif; ?>
        
     
    <script src="/new/js/jquery-3.6.4.min.js"></script>
  
    <link rel="stylesheet" href="/new/css/popup.css" />
	<meta name="google-site-verification" content="XELfvBSSbgIVQi4KQ_-apwXAGOFRoDBvrfYNIDTxtck" />
    </head>
    <body>
  
        <? if (IS_PROD == true): ?>
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KM2HBDV"
                              height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        <? endif; ?>    
        <nav class="nav-block">
            <div class="nav">
                <div class="nav-wrapper">
                    <ul class="nav-list">
                        <li><a href="<?= Url::to(['/site/index']) ?>"><li><?= Yii::t('app', 'HOME') ?></a></li>
                        <li><a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>"><li><?= Yii::t('app', 'About') ?></a></li>
                        <li><a href="<?= Url::to(['/design/index']) ?>"><li><?= Yii::t('app', 'Design') ?></a></li>
                        <li><a href="<?= Url::to(['/portfolio']) ?>"><li><?= Yii::t('app', 'Portfolio') ?></a></li>
                    </ul>
                    <a href="<?= Url::to(['/site/index']) ?>" id="logo-link" ><img  id="logo-img" src="/new/img/logo.png" /></a>
                    <div class="nav-social">

                        <ul class="nav-list">
                            <li><a href="<?= Url::to(['/blog']) ?>"><?= Yii::t('app', 'Blog') ?></a></li>
                            <li><a href="<?= Url::to(['/news/index']) ?>"><?= Yii::t('app', 'NEWS') ?></a></li>
                            <li><a href="<?= Url::to(['/contacts/index']) ?>"><?= Yii::t('app', 'CONTACTS') ?></a></li>
                        </ul>
                        <div class="nav-list nav-locale">
                            <?= LangWidget::widget(['active' => Yii::$app->language]) ?>
                        </div>    

                    </div>
                </div>
            </div>
        </nav>

        <header class="header">
            <div class="header-wrapper">
                <div class="header__catalog header_catalog_menu" >
                    <p><?= Yii::t('app', 'Catalog') ?></p>
                    <div id="header_popup" class="header__catalog-popup hidden">

                        <?= \frontend\widgets\CategoryWidget::widget(); ?>
                    </div>
                    <svg
                        class="header_catalog_menu"
                        style="cursor: pointer"
                        id="header__catalog-arrow"
                        onclick="openPopUp()"
                        width="17"
                        height="11"
                        viewBox="0 0 17 11"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        >
                    <path
                        d="M9.23866 9.68369C8.8418 10.1211 8.15429 10.1211 7.75743 9.68369L0.488652 1.67193C-0.0943613 1.02932 0.361599 -2.93898e-07 1.22927 -2.37331e-07L15.7668 7.10423e-07C16.6345 7.66989e-07 17.0905 1.02933 16.5074 1.67193L9.23866 9.68369Z"
                        fill="white"
                        />
                    </svg>
                </div>
                <? include 'search-form.php';?>
                
                <div class="mobile__header">
                    <button class="header__cart" >
                        <span class="cart__amount">(<?= Yii::$app->cart->getCount() ?>)</span>
                        <svg
                            width="30"
                            height="30"
                            viewBox="0 0 30 30"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            >
                        <path
                            d="M8.75 27.5C8.0625 27.5 7.47375 27.255 6.98375 26.765C6.49375 26.275 6.24917 25.6867 6.25 25C6.25 24.3125 6.495 23.7238 6.985 23.2338C7.475 22.7438 8.06333 22.4992 8.75 22.5C9.4375 22.5 10.0263 22.745 10.5163 23.235C11.0063 23.725 11.2508 24.3133 11.25 25C11.25 25.6875 11.005 26.2763 10.515 26.7663C10.025 27.2563 9.43667 27.5008 8.75 27.5ZM21.25 27.5C20.5625 27.5 19.9738 27.255 19.4838 26.765C18.9938 26.275 18.7492 25.6867 18.75 25C18.75 24.3125 18.995 23.7238 19.485 23.2338C19.975 22.7438 20.5633 22.4992 21.25 22.5C21.9375 22.5 22.5263 22.745 23.0163 23.235C23.5063 23.725 23.7508 24.3133 23.75 25C23.75 25.6875 23.505 26.2763 23.015 26.7663C22.525 27.2563 21.9367 27.5008 21.25 27.5ZM8.75 21.25C7.8125 21.25 7.10417 20.8383 6.625 20.015C6.14583 19.1917 6.125 18.3742 6.5625 17.5625L8.25 14.5L3.75 5H2.46875C2.11458 5 1.82292 4.88 1.59375 4.64C1.36458 4.4 1.25 4.10334 1.25 3.75C1.25 3.39584 1.37 3.09875 1.61 2.85875C1.85 2.61875 2.14667 2.49917 2.5 2.5H4.53125C4.76042 2.5 4.97917 2.5625 5.1875 2.6875C5.39583 2.8125 5.55208 2.98959 5.65625 3.21875L6.5 5H24.9375C25.5 5 25.8854 5.20834 26.0938 5.625C26.3021 6.04167 26.2917 6.47917 26.0625 6.9375L21.625 14.9375C21.3958 15.3542 21.0938 15.6771 20.7188 15.9063C20.3438 16.1354 19.9167 16.25 19.4375 16.25H10.125L8.75 18.75H22.5313C22.8854 18.75 23.1771 18.87 23.4063 19.11C23.6354 19.35 23.75 19.6467 23.75 20C23.75 20.3542 23.63 20.6513 23.39 20.8913C23.15 21.1313 22.8533 21.2508 22.5 21.25H8.75Z"
                            fill="white"
                            />
                        </svg>
                    </button>
                    <div onclick="openMenu()" class="burger__menu">
                        <svg
                            width="30"
                            height="30"
                            viewBox="0 0 30 30"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            >
                        <path
                            d="M5 22.5C4.64584 22.5 4.34875 22.38 4.10875 22.14C3.86875 21.9 3.74917 21.6033 3.75 21.25C3.75 20.8958 3.87 20.5988 4.11 20.3588C4.35 20.1188 4.64667 19.9992 5 20H25C25.3542 20 25.6513 20.12 25.8913 20.36C26.1313 20.6 26.2508 20.8967 26.25 21.25C26.25 21.6042 26.13 21.9013 25.89 22.1413C25.65 22.3813 25.3533 22.5008 25 22.5H5ZM5 16.25C4.64584 16.25 4.34875 16.13 4.10875 15.89C3.86875 15.65 3.74917 15.3533 3.75 15C3.75 14.6458 3.87 14.3488 4.11 14.1088C4.35 13.8688 4.64667 13.7492 5 13.75H25C25.3542 13.75 25.6513 13.87 25.8913 14.11C26.1313 14.35 26.2508 14.6467 26.25 15C26.25 15.3542 26.13 15.6513 25.89 15.8913C25.65 16.1313 25.3533 16.2508 25 16.25H5ZM5 10C4.64584 10 4.34875 9.88 4.10875 9.64C3.86875 9.4 3.74917 9.10334 3.75 8.75C3.75 8.39584 3.87 8.09875 4.11 7.85875C4.35 7.61875 4.64667 7.49917 5 7.5H25C25.3542 7.5 25.6513 7.62 25.8913 7.86C26.1313 8.1 26.2508 8.39667 26.25 8.75C26.25 9.10417 26.13 9.40125 25.89 9.64125C25.65 9.88125 25.3533 10.0008 25 10H5Z"
                            fill="white"
                            />
                        </svg>
                        <div id="mobile__menu" class="mobile-menu hidden">
                            <ul class="mobile-menu__links">
                                <a href="<?= Url::to(['/site/index']) ?>"><li><?= Yii::t('app', 'HOME') ?></li></a>
                                <a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>"><li><?= Yii::t('app', 'About') ?></li></a>
                                <a href="<?= Url::to(['/design/index']) ?>"><li><?= Yii::t('app', 'Design') ?></li></a>
                                <a href="<?= Url::to(['/portfolio']) ?>"><li><?= Yii::t('app', 'Portfolio') ?></li></a>
                                <a href="<?= Url::to(['/blog']) ?>"><li><?= Yii::t('app', 'Blog') ?></li></a>
                                <a href="<?= Url::to(['/contacts/index']) ?>"><li><?= Yii::t('app', 'CONTACTS') ?></li></a>
                            </ul>
                            <div class="mobile-menu__locale">
                            <?= LangWidget::widget(['active' => Yii::$app->language]) ?>
                                <? /* <a href="#"><li class="mobile-menu__locale-active">UA</li></a>
                                  <a href="#"><li>RU</li></a>
                                  <a href="#"><li>EN</li></a> */ ?>
                            </div>
                            <?/*<a href="mailto: agcity@agcity.com.ua"
                               ><p id="mobile-menu_href">agcity@agcity.com.ua</p></a
                            >*/?>
                            <div class="mobile-menu__socials">
                                <? include 'social.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>   
<? if(($msg = Alert::widget()) == true):?>
    <style>
        .alert-success {
            width: 100%;
        }
    </style>
    <div class="container">
      <?= $msg; ?>
    </div>
<? endif;?>
        <?= $content ?>
          
        <footer class="footer footer-pc">
            <div class="footer__wrapper">
                <div class="footer__wrapper-copyright">
                    <img src="/new/img/image 1.png" />
                    <p class="footer__wrapper-paragraph">Copyright @ <?=date("Y")?>, vsemerch.com.ua</p>
                </div>
                <div class="footer__wrapper-titles">
                    <a href="<?= Url::to(['/news/index']) ?>"><p><?= Yii::t('app', 'NEWS') ?></p></a>
                    <a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>"><p><?= Yii::t('app', 'About') ?></p></a>
                    <a href="<?= Url::to(['/text/index', 'slug' => 'obmen_i_vozvrat_tovara']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'Exchange and return') ?></p></a>
                </div>
                <div class="footer__wrapper-titles">
                    <a href="<?= Url::to(['/design/index']) ?>"><p><?= Yii::t('app', 'Design') ?></p></a>
                    <a href="<?= Url::to(['/production/index']) ?>"><p><?= Yii::t('app', 'Production') ?></p></a>
                    <a href="<?= Url::to(['/text/index', 'slug' => 'oplata_i_dostavka_tovara']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'Payment and delivery') ?></p></a>
                </div>
                <div class="footer__wrapper-titles">
                    <a href="<?= Url::to(['/contacts/index']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'CONTACTS') ?></p></a>
                    <div>
                       <?/* <a href="mailto: agcity@agcity.com.ua"
                           ><p class="footer__wrapper-mail">agcity@agcity.com.ua</p></a
                        >*/?>
                        <div class="socials">
                            <? include 'social.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <footer class="footer footer-mobile">
            <div class="footer__wrapper">
                <div class="footer__wrapper-copyright">
                    <img src="/new/img/image 1.png" />
                    <p class="footer__wrapper-paragraph">Copyright @ <?=date("Y")?>, vsemerch.com.ua</p>
                </div>
                <div class="footer__wrapper-titles-mobile">
                    <a href="<?= Url::to(['/news/index']) ?>"><p><?= Yii::t('app', 'NEWS') ?></p></a>
                    <a href="<?= Url::to(['/design/index']) ?>"><p style="margin-left: 17px"><?= Yii::t('app', 'Design') ?></p></a>
                    <a href="<?= Url::to(['/text/index', 'slug' => 'about']) ?>"><p><?= Yii::t('app', 'About') ?></p></a>
                    <a href="<?= Url::to(['/production/index']) ?>"><p><?= Yii::t('app', 'Production') ?></p></a>
                </div>
                <div class="footer__wrapper-titles">
                    <a href="<?= Url::to(['/text/index', 'slug' => 'obmen_i_vozvrat_tovara']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'Exchange and return') ?></p></a>
                    <a href="<?= Url::to(['/text/index', 'slug' => 'oplata_i_dostavka_tovara']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'Payment and delivery') ?></p></a>
                    <a href="<?= Url::to(['/contacts/index']) ?>"><p class="footer-title-bold"><?= Yii::t('app', 'CONTACTS') ?></p></a>
                    <?/*<a href="mailto: agcity@agcity.com.ua"><p class="footer__wrapper-mail">agcity@agcity.com.ua</p></a>*/?>
                    <div class="socials">
                        <? include 'social.php'; ?>
                    </div>
                </div>
            </div>
        </footer>
        
         <? include "popup.php"?>  
          
        <script src="/new/js/main-page/header.js"></script>
        
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script src="/new/js/slider/_slider.js"></script>
        
        <?// ob_start();?>
     
        <?php $this->endBody() ?>
      
  
       
        <script>
            $(document).ready(function(){
                $(".header__cart").click(function(){
                    
                    $.get( "<?= Url::to(['/shop/cart/list']) ?>", function( data ) {
                        $("#cart-result").html(data);
                        openCartModal();
                        
                  } );
                 }); 
                 $(".header_catalog_menu").click(function(){
                    
                     $("#mobile__menu").addClass("hidden");
                       openPopUp();
                 });
                 $(".burger__menu").click(function(){
                     $("#header_popup").addClass("hidden");
                 });
              
            });
        </script>
        <link rel="stylesheet" href="/new/css/modal-cart.css" />
    </body>
</html>
<?php $this->endPage() ?>
