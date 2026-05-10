<?php
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use frontend\models\FotosCat;

$this->title = $gallery->name;
$this->registerMetaTag(['name' => 'description', 'content' => $gallery->name]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $gallery->name]);


$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/unitegallery/css/unite-gallery.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/unitegallery/js/unitegallery.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);
$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/unitegallery/themes/default/ug-theme-default.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/unitegallery/themes/default/ug-theme-default.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);

$this->registerJs("
api = jQuery('#gallery').unitegallery();

", View::POS_READY, 'unitegallery');

//$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/bxslider/jquery.bxslider.css');
//$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/select/jquery-ui.css');
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bxslider/jquery.bxslider.min.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/main.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);

?>

<center>

    <h1><?=$gallery->name?></h1>

    <div id="gallery" style="display:none;">
                            <?foreach($gallery->fotos as $item):?>
                                <img alt="<?=$item->name?>"
                                     src="<?=Yii::$app->request->baseUrl.'/upload/fotos/ico/'.$item->image?>"
                                     data-image="<?=Yii::$app->request->baseUrl.'/upload/fotos/'.$item->image?>"
                                     data-description="<?=$item->name?>">

                            <?endforeach;?>
    </div>

    </center>