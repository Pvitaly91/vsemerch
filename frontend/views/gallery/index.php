<?
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\widgets\LinkPager;

?>
<?
$this->title = 'Valery Sakhovska';
$this->registerMetaTag(['name' => 'description', 'content' => 'Valery Sakhovska']);
$this->registerMetaTag(['name' => 'keywords', 'content' => 'Valery Sakhovska']);


$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/unitegallery/css/unite-gallery.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/unitegallery/js/unitegallery.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);
$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/unitegallery/themes/default/ug-theme-default.css');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/unitegallery/themes/default/ug-theme-default.js',['position'=>View::POS_HEAD,'depends'=>['yii\web\JqueryAsset']]);

$this->registerJs("
api = jQuery('#gallery').unitegallery();

", View::POS_READY, 'unitegallery');
?>


<center>
    <div id="gallery" style="display:none;">
        <?foreach($gallery as $item):?>
            <img alt="<?=$item->name?>"
                 src="<?=Yii::$app->request->baseUrl.'/upload/fotos/ico/'.$item->image?>"
                 data-image="<?=Yii::$app->request->baseUrl.'/upload/fotos/'.$item->image?>"
                 data-description="<?=$item->name?>">

        <?endforeach;?>
    </div>
    </center>