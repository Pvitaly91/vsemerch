<?
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\Text;
use frontend\models\Catalog;


$this->title = Yii::t('app', 'PREPARATIONS');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'PREPARATIONS')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'PREPARATIONS')]);

?>

<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h2><?=Yii::t('app', 'PREPARATIONS');?></h2>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                         Yii::t('app', 'PREPARATIONS'),
                    ],
                ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">

        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <?foreach(Catalog::find()->orderBy('sort')->all() as $key=>$item):?>
                    <li role="presentation" <?if($key==0):?>class="active"<?endif;?>><a href="#tabbox<?=$item->id?>" aria-controls="tabbox<?=$item->id?>" role="tab" data-toggle="tab"><?=$item->name?></a></li>
                    <?if(is_array($item) && $key<count($item)):?><li><span class="glyphicon glyphicon-arrow-right"></span></li><?endif;?>
                <?endforeach;?>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <?foreach(Catalog::find()->orderBy('sort')->all() as $key=>$item):?>
                    <div role="tabpanel" class="tab-pane <?if($key==0):?>active<?endif;?>" id="tabbox<?=$item->id?>">

                        <div class="row">

                            <?foreach($item->products as $key_p=>$item_p):?>
                            <div class="col-lg-4 col-md-12  col-sm-4">
                                <?= $this->render('/products/_product',['item_p'=>$item_p]) ?>
                            </div>
                            <?endforeach;?>




                        </div>




                    </div>
                <?endforeach;?>

            </div>

        </div>





    </div> </div>