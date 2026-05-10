<?
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\Text;
use frontend\models\Catalog;
use frontend\models\Disease;


$this->title = Yii::t('app', 'DISEASES');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('app', 'DISEASES')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::t('app', 'DISEASES')]);

?>

<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h2><?=Yii::t('app', 'DISEASES');?></h2>


            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                         Yii::t('app', 'DISEASES'),
                    ],
                ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">

        <?foreach(Disease::find()->orderBy('sort')->all() as $item):?>

            <div class="col-xs-12 col-sm-3">
                <?= $this->render('_disease',['item'=>$item]) ?>
            </div>

        <?endforeach;?>

</div> </div>