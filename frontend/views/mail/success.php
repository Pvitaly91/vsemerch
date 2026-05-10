<?
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'Mail send');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Mail send')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Mail send')]);
?>





<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?=Yii::t('app', 'Mail send');?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [Yii::t('app', 'Mail send')],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container cnt"><div class="col-md-12">



                <?=Yii::t('app', 'Mail send success');?>
            </div>      </div></div></div>
