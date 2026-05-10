<?
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
?>


<div class="head2">
        <div class="container">
                <div class="info2 col-md-12">
                <h1><?=$text->title;?></h1>
              
                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],
                                    'links' => [$text->title],
                                ]) ?>
                        </nav>
                </div>
        </div>
</div>
<div class="body_box">
<div class="top2"></div>
<div class="content">
<div class="container cnt"><div class="col-md-12">
  
        <div class="row pr">

            <div class="col-xs-12 col-sm-2">

            </div>
           <?/*   
            <div class="col-xs-12 col-sm-2">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['design/show','slug'=>'web-design'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_design1.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['design/show','slug'=>'web-design'])?>">
                            <?=Yii::t('app', 'WEB design (UI/UX Design)');?>
                        </a>
                    </div>
                </div>
            </div>*/?>


            <div class="col-xs-12 col-sm-3">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['design/show','slug'=>'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_design2.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['design/show','slug'=>'razrabotka_logotipa_firmennogo_stilya_slogana_brendbuka'])?>">
                            <?=Yii::t('app', 'Development (logo, corporate identity, slogan, brand book)');?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-3">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['design/show','slug'=>'dizayn_poligrafii_katalogi_buklety_prezentacii'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_design3.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['design/show','slug'=>'dizayn_poligrafii_katalogi_buklety_prezentacii'])?>">
                            <?=Yii::t('app', 'Print design any (catalogs, brochures, presentations)');?>
                        </a>
                    </div>
                </div>
            </div>



        </div>
<?=$text->getEditLink()?>
<?=$text->body;?>


        <div class="text-center">
            <a href="/catalogpdf/exemple-work.pdf" class="btn btn-danger"><span class="glyphicon glyphicon-blackboard"></span> Примеры работ</a>
        </div>

    </div>      </div></div></div>

