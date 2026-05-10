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

if(!isset($breadcrumbs)){
    $breadcrumbs[] = [
        "link" => \yii\helpers\Url::to(['site/index']),
        "label" => Yii::t('yii', 'Home')
    ];
    $breadcrumbs[] =[
       
        "label" => Yii::t('app', 'News')
    ];

    
} 
?>

<?= $this->render('/news/newslist',["news" => $news, "pages" => $pages,'breadcrumbs'=>$breadcrumbs,"section" => "news"]) ?>