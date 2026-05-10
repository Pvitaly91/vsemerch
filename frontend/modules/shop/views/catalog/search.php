<?php

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\LinkPager;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;

/* @var $this yii\web\View */
if(!empty($category)){
    $title = $category ? 'Welcome!' : $category->title;
    $this->title = Html::encode($category->meta_title);
    $this->title = ($this->title == "")?"Результати пошук":$this->title;
    $this->registerMetaTag(['name' => 'description', 'content' => $category->meta_description]);
}
//$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta_keywords]);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$this->registerCssFile('/new/css/catalog.css');

$this->registerCssFile('/new/css/_more-btn.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile('/new/js/catalog-page/index.js', ['position' => View::POS_END]);
$this->registerJsFile('/new/js/focus/focus.js', ['position' => View::POS_END]);
$this->registerCssFile('/new/css/search-page.css');
$this->registerCssFile('/new/css/fix-catalog.css');
$hiden = [
    //  "kolir",
    "rozmir"
];
?>


<main class="container">

    <section>





        <div class="sort-titles-block">
            <?
            $pagination = $productsDataProvider->getPagination();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $count = $productsDataProvider->getCount();
            $end = $begin + $count - 1;
            ?>
            <p class='item-count'><?=Yii::t('shop', 'Showed')?> <?//Показано ?> <span><?= $begin ?>-<?= $end ?></span> із <span><?= $productsDataProvider->getTotalCount() ?></span> <?=Yii::t('shop', 'Possible')?> <?//можливих?></p>
            <div class="sort-titles-block__button-container">
                <button
                    onclick="openFilter()"
                    style="background: #db4444; color: #ffffff"
                    class="hidden"
                    >
                    <?=Yii::t('shop', 'Filtr')?><svg
                        width="18"
                        height="18"
                        viewBox="0 0 18 18"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        >
                        <path
                            d="M7.675 18V12.375H9.175V14.45H18V15.95H9.175V18H7.675ZM0 15.95V14.45H6.175V15.95H0ZM4.675 11.8V9.75H0V8.25H4.675V6.15H6.175V11.8H4.675ZM7.675 9.75V8.25H18V9.75H7.675ZM11.825 5.625V0H13.325V2.05H18V3.55H13.325V5.625H11.825ZM0 3.55V2.05H10.325V3.55H0Z"
                            fill="white"
                            />
                    </svg>
                </button>
                <button>
                    <?=Yii::t('shop', 'Choose sort')?><svg
                        id="sort-arrow"
                        onclick="openSort()"
                        width="18"
                        height="11"
                        viewBox="0 0 18 11"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        >
                        <path
                            d="M9.74061 10.1837C9.34375 10.6211 8.65625 10.6211 8.25939 10.1837L0.990606 2.17193C0.407592 1.52932 0.863552 0.499999 1.73122 0.499999L16.2688 0.500001C17.1364 0.500001 17.5924 1.52933 17.0094 2.17193L9.74061 10.1837Z"
                            fill="#DB4444"
                            />
                    </svg>
                    <?php
                    if(!empty($category)):
                        $url = rawurldecode(\yii\helpers\Url::to(['/shop/catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'view-sort' => 'all', 'filters' => Yii::$app->request->get('filters')]));
                        $items = [
                            'price' => Yii::t('shop', 'From cheap to expensive'),
                            '-price' => Yii::t('shop', 'From expensive to cheap'),
                            '-novelty' => Yii::t('shop', 'Novelty'),
                            '-action' => Yii::t('shop', 'Actions'),
                        ];
                        //    echo Html::dropDownList('sort', $sort, $items, ['prompt' => Yii::t('shop', 'Choose sort'), 'onchange' => "document.location='{$url}&sort='+this.value;", 'class' => 'form-control']);
                        ?>
                        <div id="sorting-popup" class="sorting-block hidden">
                            <? foreach ($items as $k => $sortfilter): ?>
                                <div class="sorting-popup__input">
                                    <input type="checkbox" <? if (strpos($_SERVER["REQUEST_URI"], "&sort=" . $k) !== false): ?>checked<? endif; ?> onchange="document.location = '<?= $url ?>?view-sort=all&sort=<?= $k ?>';" name="input-second" />
                                    <p><?= $sortfilter ?></p>
                                </div>
                            <? endforeach; ?>

                        </div>
                    <?php endif; ?>    
                </button>
            </div>
        </div> 
        <div class="main-block">

        <? $mid =1;?>
              
            <? include "main_block.php"?>

        </div> 

        <div class="more-btn">
            <?
            echo LinkPager::widget([
                'pagination' => $pagination,
            ]);
            ?>
        </div>
        <div class="container" style="line-height: 25px; margin-top: 60px"> <? if (!empty($category->body)): ?>
                <h1 style="margin-bottom: 32px; font-size: 30px;"><?= $title ?></h1>
                <?= $category->body ?>
        <? endif; ?>
        </div>
    </section>  
</main>