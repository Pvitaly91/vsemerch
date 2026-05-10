<?php

use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use common\components\Text;

$this->title = $news->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $news->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $news->meta_keywords]);

if(!isset($breadcrumbs)){
    $breadcrumbs[] = [
        "link" => \yii\helpers\Url::to(['site/index']),
        "label" => Yii::t('yii', 'Home')
    ];
    $breadcrumbs[] =[
        "link" => \yii\helpers\Url::to(['news/index']),
        "label" => Yii::t('app', 'News')
    ];

    $breadcrumbs[] = [
        "label" => $news->title,
    ];
}    
?>

<main class="container newslist">
    <section>
        <?= $this->render('/catalog/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
        <div class="product-card-block__about">
            <div class="content"  >
                <img alt="<?=Html::encode($news->alt)?>" class="main-img" align="left" src="<?=Yii::$app->request->baseUrl.'/upload/news/big/'.$news->image?>" />
                <h1><?=$news->title; ?></h1>
                <?=$news->getEditLink()?>
                <p class='date'><?=Yii::$app->formatter->asDatetime($news->date, 'd/M/Y')?></p>
                  <?=$news->body?>
            
            </div>
        </div>
    </section>
</main>