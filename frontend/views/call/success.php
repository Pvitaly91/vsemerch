<?
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'Request a call');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Request a call')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Request a call')]);
?>


<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?=Yii::t('app', 'Request a call');?></h1>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [Yii::t('app', 'Request a call')],
                ]) ?>
            </nav>
        </div>
    </div>
</div>
<div class="container cnt">


        <div class="flash-success">
            <?=Yii::t('app', 'Request a call success');?>
        </div>


</div>
