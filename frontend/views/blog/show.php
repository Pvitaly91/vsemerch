<?
$breadcrumbs[] = [
    "link" => \yii\helpers\Url::to(['site/index']),
    "label" => Yii::t('yii', 'Home')
];
$breadcrumbs[] =[
    "link" => \yii\helpers\Url::to(['blog/index']),
    "label" => Yii::t('app', 'Blog')
];

$breadcrumbs[] = [
    "label" => $news->title,
];

?>
<?= $this->render('/news/show',["news" => $news,'breadcrumbs'=>$breadcrumbs]) ?>