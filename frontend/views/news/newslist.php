<?php
use yii\helpers\Url;

use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\components\Text;
?>

<main class="container newslist">
    <section>
        <?= $this->render('/catalog/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
        <? if(isset($h1)):?>
            <h1><?=$h1?></h1>
        <? endif;?>    
        <?foreach($news as $news):?>
            <div class="product-card-block__about">
               <div class="content"  >
                   <img alt="<?=Html::encode($news->alt)?>" class="main-img" align="left" src="<?=Yii::$app->request->baseUrl.'/upload/news/big/'.$news->image?>" style="width:300px" />
                   <h2><a href="<?=Url::to([$section.'/show','translit'=>$news->translit,'id'=>$news->id])?>"><?=$news->title; ?></a></h2>
                    <p class='date'><?=Yii::$app->formatter->asDatetime($news->date, 'd/M/Y')?></p>
                    <?=Text::getShort($news->body,600);?>
             
               </div>
           </div>
        <? endforeach;?>
        <div class="more-btn">
            <?=LinkPager::widget([
                'pagination' => $pages,
                'registerLinkTags' => true
            ]);?>
        </div>
    </section>
</main>