<?php

use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;

$this->title = $text->meta_title;
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
<?
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
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
                                    'links' => [
                                        ['label'=>Yii::t('app', 'Design'),'url'=>['design/index']],
                                        $text->title],
                                ]) ?>
                        </nav>
                </div>
        </div>
</div>
<div class="body_box">
<div class="top2"></div>
<div class="content">
<div class="container cnt"><div class="col-md-12">
  <?=$text->getEditLink()?>


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
    <?php
    if ($text->slug == 'web-design111'):?>
      <div class="box3">
      <div class="container">
          <div class="text-center"><h3>НАШИ РАБОТЫ</h3></div>
          <div class="slider1">
                              <div class="slide">

      <div class="view4">
          <a href="/img/web/web1.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/web/web1.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/web/web1.jpg"> 1</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/web/web2.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/web/web2.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/web/web2.jpg"> 2</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/web/web3.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/web/web3.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/web/web3.jpg"> 3</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/web/web4.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/web/web4.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/web/web4.jpg">4</a>
          </div>


      </div>
                  </div>






          </div>
      </div>
    <?php elseif ($text->slug== 'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka1111'):?>
      <div class="box3">
      <div class="container">
          <div class="text-center"><h3>НАШИ РАБОТЫ</h3></div>
          <div class="slider1">
                              <div class="slide">

      <div class="view4">
          <a href="/img/logo/logo1.1.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/logo/logo1.1.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo1.1.jpg"> 1</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/logo/logo1.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/logo/logo1.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo1.jpg"> 2</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/logo/logo2.2.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/logo/logo2.2.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo2.2.jpg"> 3</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/logo/logo2.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/logo/logo2.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo2.jpg">4</a>
          </div>


      </div>
                  </div>
                  <div class="slide">

                <div class="view4">
                <a href="/img/logo/logo3.jpg" rel="shadowbox[gal1]" class="fancybox">
                <img src="/img/logo/logo3.jpg" width="255" height="170" border="0" />
                </a>
                <div class="mask">
                <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo3.jpg">5</a>
                </div>


                </div>
                </div>
                <div class="slide">

              <div class="view4">
              <a href="/img/logo/logo3.1.jpg" rel="shadowbox[gal1]" class="fancybox">
              <img src="/img/logo/logo3.1.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo3.1.jpg">6</a>
              </div>


              </div>
              </div>
              <div class="slide">

            <div class="view4">
            <a href="/img/logo/logo4.jpg" rel="shadowbox[gal1]" class="fancybox">
            <img src="/img/logo/logo4.jpg" width="255" height="170" border="0" />
            </a>
            <div class="mask">
            <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo4.jpg">7</a>
            </div>


            </div>
            </div>
            <div class="slide">

          <div class="view4">
          <a href="/img/logo/logo4.4.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/logo/logo4.4.jpg" width="255" height="170" border="0" />
          </a>
          <div class="mask">
          <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/logo4.4.jpg">8</a>
          </div>


          </div>
          </div>
          <div class="slide">

        <div class="view4">
        <a href="/img/logo/style1.jpg" rel="shadowbox[gal1]" class="fancybox">
        <img src="/img/logo/style1.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/style1.jpg">9</a>
        </div>


        </div>
        </div>
        <div class="slide">

      <div class="view4">
      <a href="/img/logo/style2.jpg" rel="shadowbox[gal1]" class="fancybox">
      <img src="/img/logo/style2.jpg" width="255" height="170" border="0" />
      </a>
      <div class="mask">
      <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/style2.jpg">10</a>
      </div>


      </div>
      </div>
      <div class="slide">

    <div class="view4">
    <a href="/img/logo/style3.jpg" rel="shadowbox[gal1]" class="fancybox">
    <img src="/img/logo/style3.jpg" width="255" height="170" border="0" />
    </a>
    <div class="mask">
    <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/style3.jpg">11</a>
    </div>


    </div>
    </div>
    <div class="slide">

  <div class="view4">
  <a href="/img/logo/style4.jpg" rel="shadowbox[gal1]" class="fancybox">
  <img src="/img/logo/style4.jpg" width="255" height="170" border="0" />
  </a>
  <div class="mask">
  <a rel="shadowbox[gal]" class="fancybox" href="/img/logo/style4.jpg">12</a>
  </div>


  </div>
  </div>

          </div>
      </div>
    <?php elseif ($text->slug== 'dizayn_poligrafii_katalogi_buklety_prezentacii11111111'):?>
      <div class="box3">
      <div class="container">
          <div class="text-center"><h3>НАШИ РАБОТЫ</h3></div>
          <div class="slider1">
                              <div class="slide">

      <div class="view4">
          <a href="/img/printing/1.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/printing/1.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/1.jpg"> 1</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/printing/2.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/printing/2.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/2.jpg"> 2</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/printing/3.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/printing/3.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/3.jpg"> 3</a>
          </div>


      </div>
                  </div>
                              <div class="slide">

      <div class="view4">
          <a href="/img/printing/4.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/printing/4.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/4.jpg">4</a>
          </div>


      </div>
                  </div>
                  <div class="slide">

                <div class="view4">
                <a href="/img/printing/5.jpg" rel="shadowbox[gal1]" class="fancybox">
                <img src="/img/printing/5.jpg" width="255" height="170" border="0" />
                </a>
                <div class="mask">
                <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/5.jpg">5</a>
                </div>


                </div>
                </div>
                <div class="slide">

              <div class="view4">
              <a href="/img/printing/6.jpg" rel="shadowbox[gal1]" class="fancybox">
              <img src="/img/printing/6.jpg" width="255" height="170" border="0" />
              </a>
              <div class="mask">
              <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/6.jpg">6</a>
              </div>


              </div>
              </div>
              <div class="slide">

            <div class="view4">
            <a href="/img/printing/7.jpg" rel="shadowbox[gal1]" class="fancybox">
            <img src="/img/printing/7.jpg" width="255" height="170" border="0" />
            </a>
            <div class="mask">
            <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/7.jpg">7</a>
            </div>


            </div>
            </div>
            <div class="slide">

          <div class="view4">
          <a href="/img/printing/8.jpg" rel="shadowbox[gal1]" class="fancybox">
          <img src="/img/printing/8.jpg" width="255" height="170" border="0" />
          </a>
          <div class="mask">
          <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/8.jpg">8</a>
          </div>


          </div>
          </div>
          <div class="slide">

        <div class="view4">
        <a href="/img/printing/9.jpg" rel="shadowbox[gal1]" class="fancybox">
        <img src="/img/printing/9.jpg" width="255" height="170" border="0" />
        </a>
        <div class="mask">
        <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/9.jpg">9</a>
        </div>


        </div>
        </div>
        <div class="slide">

      <div class="view4">
      <a href="/img/printing/10.jpg" rel="shadowbox[gal1]" class="fancybox">
      <img src="/img/printing/10.jpg" width="255" height="170" border="0" />
      </a>
      <div class="mask">
      <a rel="shadowbox[gal]" class="fancybox" href="/img/printing/10.jpg">10</a>
      </div>


      </div>
      </div>



          </div>
      </div>
    <?php endif;
    ?>
