<?php
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use common\components\Text;

$this->title = $news->meta_title;
?>
<div class="head2 7">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?=Yii::t('app', 'Статьи');?></h1>
            <nav class="breadcrumbs">
            <?= Breadcrumbs::widget([
                'homeLink' => [ 
                      'label' => Yii::t('yii', 'Home'),
                      'url' => ['site/index'],
                 ],                
                'links' => [
                            ['label'=>Yii::t('app', 'Статьи'),'url'=>['news/index']],
                            $news->title
                            ],
            ]) ?>

            </nav>
        </div>
    </div>
</div>
<div class="container cnt"><div class="col-md-12">

	<h1><?=$news->title?></h1>
        
            <?=$news->body?>
            <p class='date'><?=Yii::$app->formatter->asDatetime($news->date, 'd/M/Y')?></p>
            <div class="both"></div>



</div></div>
        
        