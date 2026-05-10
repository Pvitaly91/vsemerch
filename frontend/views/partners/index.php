<?
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\models\News;
use yii\widgets\LinkPager;
use common\components\Text;
?>
<?
$this->title = Yii::t('app', 'PARTNERS');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'PARTNERS')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'PARTNERS')]);
?>


<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?=Yii::t('app', 'PARTNERS');?></h1>
        
            <nav class="breadcrumbs">
            <?= Breadcrumbs::widget([
                'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => ['site/index'],
                 ],                
                'links' => [
                            Yii::t('app', 'PARTNERS')
                            ],
            ]) ?>
            </nav>


        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">
        
        <div class="row">
        <?foreach($model as $item):?>
            <div class="col-xs-12 col-sm-6">
            <div class="media partner">
                <div class="media-left">
                    <a href="<?=$item->url?>" target="_blank"><img src="<?=Yii::$app->request->baseUrl.'/upload/partners/'.$item->image?>" class="pic" border="0"  /></a>
                </div>
                <div class="media-body content-partner">
                    <?=nl2br($item->body)?>
                </div>
            </div>
            </div>
        <?endforeach;?>
        </div>

        

                                        
</div></div>