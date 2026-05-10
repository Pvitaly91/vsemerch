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

            <div class="col-xs-12 col-sm-6">
                <div class="view3 pull-right">
                    <div class="circl">
                        <a href="<?=Url::to(['catalog/index'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_production1.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::toRoute(['catalog/index'])?>">
                            <?=Yii::t('app', 'Printing and souvenirs');?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="view3 pull-left">
                    <div class="circl">
                        <a href="<?=Url::to(['production/show','slug'=>'naruzhnaya_reklama'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_production2.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['production/show','slug'=>'naruzhnaya_reklama'])?>">
                            <?=Yii::t('app', 'OUTDOOR ADVERTISING');?>
                        </a>
                    </div>
                </div>
            </div>

<?/*
            <div class="col-xs-12 col-sm-2">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['production/show','slug'=>'izgotovlenie_reklamnyh_konstrukciy'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_production3.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['production/show','slug'=>'izgotovlenie_reklamnyh_konstrukciy'])?>">
                            <?=Yii::t('app', 'Advertising constructions');?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-2">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['production/show','slug'=>'brendirovanie_vystavochnyh_stendov_i_avtotransporta'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_production4.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['production/show','slug'=>'brendirovanie_vystavochnyh_stendov_i_avtotransporta'])?>">
                            <?=Yii::t('app', 'Branding EXHIBITION STANDS AND AUTO');?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-2">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['production/show','slug'=>'promoakcii'])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/pr_ico_production5.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['production/show','slug'=>'promoakcii'])?>">
                            <?=Yii::t('app', 'PROMOTION');?>
                        </a>
                    </div>
                </div>
            </div>
*/?>
        </div>


<?=$text->body;?>
        <?/**
        <br>
<p style="text-align:center">
<span style="font-size:18px; font-family:arial,helvetica,sans-serif">  <?=Yii::t('app', 'Вы можете ознакомиться с каталогом наших продуктов по ссылке ниже!')?></span>
</p>

<div style="display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;">
  <a href="https://agcity.com.ua/catalogpdf/AGPROMOmin.pdf" class="btncatalog"  target="_blank"  style="margin-top:20px"><?=Yii::t('app', 'Каталог Promo-продукции')?> </a>
</div>
 **/?>
    </div>      
    </div></div></div>


