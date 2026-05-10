<?php
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;

$this->title = $model->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->meta_keywords]);


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
                <h1><?=$model->name;?></h1>
               

                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],

                                    'links' => [
                                        ['label'=>Yii::t('app', 'Production'),'url'=>['production/index']],
                                        ['label'=>Yii::t('app', 'Printing and souvenirs'),'url'=>['catalog/index']],
                                        $model->name
                                    ],
                                ]) ?>
                        </nav>
                </div>
        </div>
</div>
<div class="body_box">
<div class="top2"></div>
<div class="content">
<div class="container cnt"><div class="col-md-12">
 <?=$model->getEditLink()?>
                <img src="<?=Yii::$app->request->baseUrl.'/upload/catalog/big/'.$model->imageAvatar?>" width="500" height="500" border="0" class="img-responsive pic" alt="<?=Html::encode($model->alt)?>" align="left" />

                <?=$model->body?>







    </div>


</div></div></div>

<?if(!empty($model->products)):?>
    <div class="box3">
        <div class="container">
            <div class="text-center"><h3><?=Yii::t('app', 'OUR WORK')?></h3></div>
            <div class="slider1">
                <?foreach($model->products as $item):?>
                    <div class="slide">
                        <?= $this->render('/products/_product',['item_p'=>$item]) ?>
                    </div>
                <?endforeach;?>

            </div>
        </div>
    </div>
<?endif;?>
