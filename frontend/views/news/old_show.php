<?php
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use common\components\Text;

$this->title = $news->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $news->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $news->meta_keywords]);
?>
<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?=Yii::t('app', 'News');?></h1>
            <nav class="breadcrumbs">
            <?= Breadcrumbs::widget([
                'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => ['site/index'],
                 ],                
                'links' => [
                            ['label'=>Yii::t('app', 'News'),'url'=>['news/index']],
                            $news->title
                            ],
            ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">

	<h1><?=$news->title?></h1>
          <?=$news->getEditLink()?>
            <img src="<?=Yii::$app->request->baseUrl.'/upload/news/big/'.$news->image?>" alt="<?=Html::encode($news->alt)?>" width="400" height="400" border="0" align="left" class='img-responsive img-thumbnail pic' />
            <?=$news->body?>
            <p class='date'><?=Yii::$app->formatter->asDatetime($news->date, 'd/M/Y')?></p>
            <div class="both"></div>



</div></div>
        
        