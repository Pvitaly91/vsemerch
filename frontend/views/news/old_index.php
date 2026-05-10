<?php
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\components\Text;

$this->title = Yii::t('app', 'News');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'News')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'News')]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

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
                            Yii::t('app', 'News')
                            ],
            ]) ?>
            </nav>


        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">
        
        <?foreach($news as $item):?>
    <div class="media news">
        <div class="media-left">
                <a href="<?=Url::to(['news/show','translit'=>$item->translit,'id'=>$item->id])?>"><img src="<?=Yii::$app->request->baseUrl.'/upload/news/ico/'.$item->image?>" alt="<?=Html::encode($item->title)?>" width="250" height="180" border="0" class="media-object img-responsive img-thumbnail" /></a>
        </div>
        <div class="media-body content-news">
            <p class="date"><?=Yii::$app->formatter->asDatetime($item->date, 'd/M/Y')?></p>
                <a href="<?=Url::to(['news/show','translit'=>$item->translit,'id'=>$item->id])?>" class="name"><?=$item->title?></a>
		<p><?=Text::getShort($item->body,400);?></p><div class="both"></div>
        </div>
    </div>
        <?endforeach;?>
        
        <div class="both"></div>
        <?=LinkPager::widget([
            'pagination' => $pages,
            'registerLinkTags' => true
        ]);?>        
        

                                        
</div></div>