<?
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
?>
<?
$this->title = Yii::t('app', 'Printing and souvenirs');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'Printing and souvenirs')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'Printing and souvenirs')]);
?>


<div class="head2">
        <div class="container">
                <div class="info2 col-md-12">
                <h1><?=Yii::t('app', 'Printing and souvenirs');?></h1>


                        <nav class="breadcrumbs">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => Yii::t('yii', 'Home'),
                                        'url' => ['site/index'],
                                    ],
                                    'links' => [
                                        ['label'=>Yii::t('app', 'Production'),'url'=>['production/index']],
                                        Yii::t('app', 'Printing and souvenirs')],
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

            <?foreach($model as $item):?>

            <div class="col-xs-12 col-sm-2">
                <div class="view3">
                    <div class="circl">
                        <a href="<?=Url::to(['catalog/show','translit'=>$item->translit])?>">
                            <img src="<?=Yii::$app->request->BaseUrl?>/img/catalog/ico<?=$item->id?>.png" width="113" height="113" border="0" />
                        </a>
                    </div>
                    <div class="mask">
                        <a href="<?=Url::to(['catalog/show','translit'=>$item->translit])?>">
                            <?=$item->name;?>
                        </a>
                    </div>
                </div>
            </div>

            <?endforeach;?>



        </div>


    </div>      </div></div></div>

