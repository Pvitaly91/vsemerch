<?
use yii\widgets\Breadcrumbs;
?>
<?
$this->title = Yii::t('app', 'Web_meta_title');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Web_meta_description')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Web_meta_keywords')]);
?>


<div class="head2">
        <div class="container">
                <div class="info2 col-md-12">
                <h1><?=Yii::t('app', 'Web');?></h1>


                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],
                                    'links' => [Yii::t('app', 'Web')],
                                ]) ?>
                        </nav>
                </div>
        </div>
</div>
<div class="body_box">
<div class="top2"></div>
<div class="content">
<div class="container cnt"><div class="col-md-12">

        <div class="row">
            <div class="col-xs-12 col-sm-3">
                <?= $this->render('_left') ?>
            </div>

            <div class="col-xs-12 col-sm-9">
                <?foreach($model as $item):?>

                    <div  class="col-xs-12 col-sm-4">
                        <?= $this->render('_product',['item_p'=>$item]) ?>
                    </div>

                <?endforeach;?>
            </div>
        </div>
    </div>      </div></div></div>

