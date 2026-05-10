<?
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\Text;
use yii\helpers\Url;


$this->title = (!empty($product->meta_title))?$product->meta_title:$product->name;
$this->registerMetaTag(['name' => 'description', 'content' => ((!empty($product->meta_description))?$product->meta_description:$product->name)]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $product->meta_keywords]);

?>

<div class="head2 222">
    <div class="container">
        <div class="info2 col-md-12">
            <h2><?=$product->catalog->name;?></h2>


            <nav class="breadcrumbs">
            <?= Breadcrumbs::widget([
                'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => ['site/index'],
                 ],                 
                'links' => [
                    ['label'=>Yii::t('app', 'PREPARATIONS'),'url'=>['products/index']],
                            $product->name,
                            ],
            ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">
<h1><?=$product->name?></h1>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
        <img property="image" src="<?=Yii::$app->request->baseUrl.'/upload/products/big/'.$product->image?>" width="500" height="500" border="0" class="img-responsive" alt="<?=$product->name?>" />


            <a href="<?=(!empty($product->pdf)) ? Url::to('/upload/products/pdf/'.$product->pdf) : '#'?>" class="btn_pdf"><?=Yii::t('app', 'Download PDF Instructions')?></a>
            </div>
            <div class="col-xs-12 col-sm-6">
        <?=$product->body?>
            </div>
            </div>

 </div> </div>