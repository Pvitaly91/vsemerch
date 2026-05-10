<?
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\Text;
use yii\helpers\Url;


$this->title = (!empty($disease->meta_title))?$disease->meta_title:$disease->name;
$this->registerMetaTag(['name' => 'description', 'content' => ((!empty($disease->meta_description))?$disease->meta_description:$disease->name)]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $disease->meta_keywords]);

?>

<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h2><?=Yii::t('app', 'DISEASES')?></h2>


            <nav class="breadcrumbs">
            <?= Breadcrumbs::widget([
                'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => ['site/index'],
                 ],                 
                'links' => [
                    ['label'=>Yii::t('app', 'DISEASES'),'url'=>['disease/index']],
                            $disease->name,
                            ],
            ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">
<h1><?=$disease->name?></h1>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                 <img src="<?=Yii::$app->request->baseUrl.'/upload/disease/big/'.$disease->imageAvatar?>" width="500" height="500" border="0" class="img-responsive" alt="<?=$disease->name?>" />




                <div class="accordion-container">
                    <?php foreach($disease->type as $item):?>
                        <div id="tab<?=$item->id?>" class="set">
                            <a href="#tab<?=$item->id?>">
                                <?=$item->title?>
                                <i class="fa fa-plus"></i>
                            </a>
                            <div class="content_tab">
                                <?=$item->body?>
                                <div class="both"></div>
                                <?if(!empty($item->product)):?>
                                <div class="row">
                                <div class="col-lg-11">
                                    <?= $this->render('/products/_product',['item_p'=>$item->product]) ?>
                                </div>
                                </div>
                                <?endif;?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>






            </div>
            <div class="col-xs-12 col-sm-6">
        <?=$disease->body?>
            </div>
            </div>

 </div> </div>