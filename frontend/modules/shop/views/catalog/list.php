<?php

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\LinkPager;
use yii\widgets\Breadcrumbs;
use frontend\modules\shop\widgets\FilterCheckboxWidget;
use dmstr\widgets\Alert;
/* @var $this yii\web\View */
$title = $category === null ? 'Welcome!' : $category->title;
$this->title = Html::encode($category->meta_title);
$this->registerMetaTag(['name' => 'description', 'content' => $category->meta_description]);
//$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta_keywords]);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$this->registerCssFile('/new/css/catalog.css');
$this->registerCssFile('/new/css/fix.css');
$this->registerCssFile('/new/css/_more-btn.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile('/new/js/catalog-page/index.js',['position'=>View::POS_END]);
$this->registerJsFile('/new/js/focus/focus.js',['position'=>View::POS_END]);

 $hiden =[
                //  "kolir",
                "rozmir",
                "size",
                "sizeclothes",
                "freightpacking",
                "individualna-upakovka",
                "grupa-nanesenna",
                "grupa-nanesenna"
            ];
 $hiden = array_merge($hiden ,\common\Helpers\Partners::$ext["eney"]);
 $empty = new common\models\Product();
 if(isset(\common\Helpers\Partners::$partners[$category->partner]) && \common\Helpers\Partners::$partners[$category->partner]){
      $isPatner = true;
 }else
    $isPatner = false;

    $extFilteValuesMap["47"]["tm"] = ["id","id-identity"];
	$extFilteValuesMap["39"]["vid-rukzaka"] = ["rukzak-dla-noutbuka"];

    $extPropsForCat["39"] = ["vid-rukzaka","obem"];
   
 ?>
<? if($empty->isAdmin()):?>
    <script>
        $(document).ready(function(){
            $(".delSubCatMap").click(function(event){
                if (confirm("Удалить склейку?") != true) {
                    event.preventDefault();
                }
            });
            $(".delOptionMap").click(function(event){
                event.preventDefault();
                if (confirm("Press a button!") == true) {
                     
                      $.ajax({
                        url: "/admin/options-map/delete?id="+$(this).attr("data-id"),
                        
                        success: function( result ) {
                          location.reload();
                        }
                      });
                  } 
            })
            
            $('.products-block__cart-btncart').click(function(event){
                event.preventDefault();
            });
        });
    </script>
<? endif;?>
<main class="container">
    <?=$category->getEditLink()?>
    <?//=$category->mappedItems(); ?>
    <section>
        <div id="filter-settings" class="filter-settings-block hidden">
                 
            <button onclick="unCheckBoxAll()" class="filter-settings-clearbtn"><?=Yii::t('shop', 'Clear filters')?></button>
            <p><?=Yii::t('shop', 'Filtr')?></p>
            <button class="filter-settings-closebtn" onclick="openFilter()">
                <svg
                    width="12"
                    height="12"
                    viewBox="0 0 12 12"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1.1875 11.6875L0.3125 10.8125L5.125 6L0.3125 1.1875L1.1875 0.3125L6 5.125L10.8125 0.3125L11.6875 1.1875L6.875 6L11.6875 10.8125L10.8125 11.6875L6 6.875L1.1875 11.6875Z"
                        fill="white"
                    />
                </svg>
            </button>
        </div>
         <? //dd($map); 
               // unset($map['naavnist']);
                $map['naavnist']["main"] = "main";
                $map['kolir']["main"] = "main";
                $map['stat']["main"] = "main";
                $map['material']["main"] = "main";
               // $map['naavnist']["main"] = "main";
                ?>
         <?
                
                $propId = NULL;
                ?>
        <div id="filter-mobile" class="filter-block-mobile hidden">
              <?php foreach ($filters as $filter):?>

                     <? if(  
                        $isPatner == false &&(in_array(trim($filter['filters'][0]->slug_option), $hiden)
                        || (!isset($map[$filter['filters'][0]->slug_option])) && !$empty->isAdmin())
                        || (isset($extPropsForCat[$category->id]) && in_array($filter['filters'][0]->slug_option, $extPropsForCat[$category->id] ))
                    ){   continue;}?>
                   <? if(trim($filter['filters'][0]->slug_option) == "freightpacking") continue;?>
                    <div class="filter-block-mobile__item">
                        <p class="filter-block-mobile__item-title">
                    <?=$empty->getPartnerPropertyTrans($filter['parent'],$propId)?>
                    <?=$empty->getOptionEditLink($propId)?> 
                    <?=$empty->getOptionMergeLink($filter['filters'][0]->slug_option,$cat_id,$propId)?>
                        </p>
                        <div class="filter-block-mobile__item-labels">
                            <?php foreach ($filter['filters'] as $item):?>
                                <?if(isset($extFilteValuesMap[$category->id][$item->slug_option]) && in_array($item->slug,$extFilteValuesMap[$category->id][$item->slug_option])){
                                    continue;
                                }?>
                                 <label>
                                    <?=FilterCheckboxWidget::widget(['url' => ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort],'item' => $item, 'productsQuery' => $productsQueryClone])?> 
                                 </label>
                            <? endforeach;?>
                               
                            </label>
                        
                        </div>
                    </div>
                    <?   $propId = NULL;?>
                <? endforeach;?>
           
            
            <div  class="more-btn clear-filter">
                     <?=Html::a(Yii::t('shop', 'Clear filters'), ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort])?>
                    
                </div>
        </div>
        
   
       
        <div class="navigation-block">
            <a href="<?=\yii\helpers\Url::to(['/site/index'])?>"><span><?=Yii::t('yii', 'Home')?></span></a>
            <svg
                width="5"
                height="8"
                viewBox="0 0 5 8"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
            </svg>
            <p><?=Yii::t('shop', 'Shop')?></p>
             <svg
                width="5"
                height="8"
                viewBox="0 0 5 8"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
            </svg>
            <? if ($prentCategoryName): ?>
                <p><?=$prentCategoryName ?></p>
                <svg
                    width="5"
                    height="8"
                    viewBox="0 0 5 8"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
                </svg>
            <? endif; ?>
            <p><?=$title?></p>
        </div>
        <div class="sort-titles-block">
            <?
            $pagination  = $productsDataProvider->getPagination();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $count = $productsDataProvider->getCount();
            $end = $begin + $count - 1;
            
            ?>
            <p class='item-count'> <?=Yii::t('shop', 'Showed')?><?//Показано ?> <span><?=$begin?>-<?=$end?></span> <?=Yii::t('shop', 'From')?> <span><?=$productsDataProvider->getTotalCount()?></span> <?=Yii::t('shop', 'Possible')?> <?//можливих?></p>
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
                        <? foreach($items as $k => $sortfilter):?>
                        <div class="sorting-popup__input">
                            <input type="checkbox" <? if(strpos($_SERVER["REQUEST_URI"],"&sort=".$k) !== false):?>checked<?endif;?> onchange="document.location='<?=$url?>?view-sort=all&sort=<?=$k?>';" name="input-second" />
                            <p><?=$sortfilter?></p>
                        </div>
                        <? endforeach;?>
                   
                    </div>
                </button>
            </div>
        </div> 
        <? //dd($hiden);?>
        <? if($empty->isAdmin()):?>
        <form action="/admin/product-map/index" method="GET">
            <input type="hidden" name="refer" value="<?=$_SERVER['REQUEST_URI']?>">
            
            <div class="more-btn"><button class="editLinck" style="    font-weight: 100;" type="submit">Переместить выбрание товари</button></div>
        <? endif;?>
        <div class="main-block">
            <div class="filter-block">
               
                <? $p = [];?>
               <?php foreach ($filters as $filter):?> 
                    <? // $p[$filter['filters'][0]->partner] = $filter['filters'][0]->partner?>   
                    <?// if(in_array($filter['filters'][0]->slug_option, $hiden) || (!isset($map[$filter['filters'][0]->slug_option]) && !$empty->isAdmin())){   continue;}?>
                    <? //if(in_array($filter['filters'][0]->slug_option, $hiden)  && !$empty->isAdmin()){   continue;}?>
               
                <? if( $isPatner == false &&(in_array(trim($filter['filters'][0]->slug_option), $hiden)
                    || (!isset($map[$filter['filters'][0]->slug_option])) && !$empty->isAdmin())
                    || (isset($extPropsForCat[$category->id]) && in_array($filter['filters'][0]->slug_option, $extPropsForCat[$category->id] ))
                ){   continue;}?>
                   <? if(trim($filter['filters'][0]->slug_option) == "freightpacking") continue;?>
                    <div class="filter-block__item">
                        <p class="filter-block__item-title" <? if(!isset($map[$filter['filters'][0]->slug_option]) && $isPatner == false):?>style="color:#bbb;"<? endif;?> ><?//=$filter['filters'][0]->slug_option?>
                                <?//=$filter['filters'][0]->slug_option; ?>
                                <?=$empty->getPartnerPropertyTrans($filter['parent'],$propId)?>       
                                <?=$empty->getOptionEditLink($propId)?> 
                               
                                <?=$empty->getOptionMergeLink($filter['filters'][0]->slug_option,$cat_id,$_map)?> 
                                
                                <?=$empty->getMappedPropsEditLink($filter['filters'][0]->slug_option,$cat_id,$propId);?></p>
                        <div class="filter-block__item-labels">
                           
                            <?php foreach ($filter['filters'] as $item):?>
                                <?if(isset($extFilteValuesMap[$category->id][$item->slug_option]) && in_array($item->slug,$extFilteValuesMap[$category->id][$item->slug_option])){
                                    continue;
                                }?>
                                 <label >
 
                                    <?=FilterCheckboxWidget::widget(['url' => ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort],'item' => $item, 'productsQuery' => $productsQueryClone])?> 
                                 </label>
                            <? endforeach;?>
                               
         
                        </div>
                    </div>
                <? $propId = NULL;?>
                <? endforeach;?>
            
                <div  class="more-btn clear-filter">
                     <?=Html::a(Yii::t('shop', 'Clear filters'), ['catalog/list', 'slug' => $category->slug, 'id' => $category->id, 'sort' => $sort])?>
                    
                </div>
            </div>
            <? $mid =3;?>
            <? include "main_block.php"?>
            
        </div> 
        <? if($empty->isAdmin()):?>
         </form>
        <? endif;?>
        <div class="more-btn">
             
             <?echo LinkPager::widget([
                'pagination' => $pagination,
                 ]);?>
        </div>
     <div class="container" style="line-height: 25px; margin-top: 60px"> <?if(!empty($category->body)):?>
         <h1 style="margin-bottom: 32px; font-size: 30px;"><?=$title?></h1>
                    <?=$category->body?>
                    <?endif;?>
    </div>
    </section>  
</main>
<link href="/new/css/fix-catalog.css" rel="stylesheet">
