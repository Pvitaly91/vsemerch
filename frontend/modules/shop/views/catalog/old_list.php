<?php

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;

/* @var $this yii\web\View */
$title = $category === null ? 'Welcome!' : $category->title;
$this->title = Html::encode($category->meta_title);
$this->registerMetaTag(['name' => 'description', 'content' => $category->meta_description]);
//$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta_keywords]);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$this->registerCssFile(Yii::$app->request->BaseUrl.'/js/fancyBox/source/jquery.fancybox.css?v=2.1.5', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/fancyBox/source/jquery.fancybox.js?v=2.1.5',['position'=>View::POS_END,'depends'=>['yii\web\JqueryAsset']]);
$this->registerJs("
$('.fancybox').fancybox();

", yii\web\View::POS_READY, 'fancybox');

$script = " $(document).ready(function () {
 

    $('.add_to_cart').click(function () {
        dataId = $(this).attr('data-id');
        dataPrice = $(this).attr('data-price');";
      //  console.log(dataId+' '+dataPrice);
      if(IS_PROD == true){
          $script .= " gtag('event', 'add_to_cart', { 
                        'send_to': 'AW-10941815611',
                        'value': dataPrice,
                        'items': [{
                        'id': dataId,
                        'google_business_vertical': 'retail'
                        }]
                    });";
      }
    $script .=   "if($(this).attr('disabled')) {
            return false;
        }
        var url = $(this).attr('href');
        var size_id = $(this).data('size_id');
        if(size_id) {
            url += '&size_id=' + size_id;
        }
        $.get(url, function (data) {
            reloadCart();
            $('#modalCart').modal({'show': true});
        });
        return false;
    });

  


});";
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="head2 ">
    <div class="container">
        <div class="info2 col-md-12">
            <!-- <h1><?= Html::encode($title) ?></h1> -->

            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        ['label' => Yii::t('shop', 'Shop')],
                        $title
                    ],
                ]) ?>
            </nav>


        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="list_content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <p>
                    <a href="#" id="btn-filters" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span> Фильтры</a>
                    </p>
                    <div id="leftsidebar">
                        <?if(Yii::$app->request->get('filters')):?>
                        <p class="text-center">
                            <?=Html::a(Yii::t('shop', 'Clear filters'), ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort], ['class' => 'btn btn-sm btn-default'])?>
                        </p>
                        <?endif;?>
                        <? $hiden =[
                          //  "kolir",
                            "rozmir"
                        ];?>
                    <?php foreach ($filters as $filter):?>
                        
                        <? if(in_array($filter['filters'][0]->slug_option, $hiden)){   continue;}?>
                       
                        <div class="panel panel-default">
                            <div class="panel-heading"><?=$filter['parent']?></div>
                            <div class="panel-body">
                             
                                <?php foreach ($filter['filters'] as $item):?>
                                  
                                   <? /* if($item->slug_option == "kolir"):?> <p style="background-color: <?=$item->value?>; width:30px; height:30px; border: 1px solid black;"></p><? endif;*/ ?><p><?=FilterCheckboxWidget::widget(['url' => ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort],'item' => $item, 'productsQuery' => $productsQueryClone])?></p>
                                <?php endforeach;?>
                            </div>
                        </div>
                    <?php endforeach;?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-8">
                            <h1><?=$category->title?></h1>
                            <?=$category->getEditLink()?>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $url = rawurldecode(\yii\helpers\Url::to(['/shop/catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'view-sort' => 'all', 'filters' => Yii::$app->request->get('filters')]));
                            $items = [
                                'price' => Yii::t('shop', 'From cheap to expensive'),
                                '-price' => Yii::t('shop', 'From expensive to cheap'),
                                '-novelty' => Yii::t('shop', 'Novelty'),
                                '-action' => Yii::t('shop', 'Actions'),
                            ];
                            echo Html::dropDownList('sort', $sort, $items, ['prompt' => Yii::t('shop', 'Choose sort'), 'onchange' => "document.location='{$url}&sort='+this.value;", 'class' => 'form-control']);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                    <?= ListView::widget([
                        'dataProvider' => $productsDataProvider,
                        'itemView' => '_product',
                    ]) ?>
                    </div>
                    <?if(!empty($category->body)):?>
                    <hr />
                    <?=$category->body?>
                    <?endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content" style="background-image: url(/img/disignweb/bg-5.png);background-size: cover;">
    <div class="container">
        <?=$category->seo?>
    </div>
</div>