<?
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\models\Catalog;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\LinkPager;
use app\models\Fasovka;
use app\models\Type;
use app\models\Brends;
?>
<?
$this->title = Yii::t('app', 'search');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'search')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'search')]);

?>
<div class="wrap">
    <div class="layout">

            <nav class="bread-crumbs">
            <?= Breadcrumbs::widget([
                'links' => [
                    Yii::t('app', 'search')
                            ],
            ]) ?>
                            <div class="both"></div>
            </nav>


        <h1><?=Yii::t('app', 'search')?></h1>
        
	<div class="ten"></div>
        <?=empty($products)?'Не чего не найдено!':''?>
	<?foreach($products as $item):?>        
            <?= $this->render('_product',['item'=>$item,'num'=>4]) ?>
        <?endforeach;?>
        <div class="both"></div>
        <?=LinkPager::widget([
            'pagination' => $pages,
            'registerLinkTags' => true
        ]);?>

        
        
    </div>
    
</div>    
                                        