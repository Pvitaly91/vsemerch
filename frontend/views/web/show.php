<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Web');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Web')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Web')]);
?>


<div class="head2">
        <div class="container">
                <div class="info2 col-md-12">
                <h1><?=$model->name;?></h1>


                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],
                                    'links' => [
                                        ['label'=>Yii::t('app', 'Web'),'url'=>['web/index']],
                                        $model->name
                                    ],

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
                <div class="row">
                <?foreach($model->products as $item):?>

                    <div  class="col-xs-12 col-sm-4">
                        <?= $this->render('_product',['item_p'=>$item]) ?>
                    </div>

                <?endforeach;?>
                </div>
                <?if($model->id==5):?>

                <div class="row pr">

                    <div class="col-xs-12 col-sm-3">
                        <div class="view3">
                            <div class="circl">
                                    <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_seo1.png" width="113" height="113" border="0" />
                            </div>
                            <div class="mask">
                                    <?=Yii::t('app', 'SEO-optimization');?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-3">
                        <div class="view3">
                            <div class="circl">
                                <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_seo2.png" width="113" height="113" border="0" />
                            </div>
                            <div class="mask">
                                <?=Yii::t('app', 'SMM-promotion');?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-3">
                        <div class="view3">
                            <div class="circl">
                                <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_seo3.png" width="113" height="113" border="0" />
                            </div>
                            <div class="mask">
                                <?=Yii::t('app', 'Contextual advertising');?>
                            </div>
                        </div>
                    </div>

                 </div>

                <?endif;?>

                <?=$model->body?>
            </div>
        </div>


    </div>      </div></div></div>

