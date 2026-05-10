<?php

namespace console\controllers;

use common\Helpers\Partners;
use common\models\Category;
use common\models\CategoryTranslate;
use yii\console\Controller;

use yii\helpers\Url;
use yii\web\Response;
class GooglexmlController extends Controller {
    public $serverName = "https://vsemerch.com.ua";
    public $layout = 'empty';
    function dd($var){
        echo "<pre>";
        print_r($var);
        echo "</pre>";
        exit;
    }
    function makeArrey($products){
        $categories =  $result = [];
       
        foreach($products as $k => $v){
            if(!empty($v->size) || $v->price != "0.00" && !empty($v->images) && is_array($v->images) && $v->slug ){
                if(!in_array($v->category_id,$categories))
                    $categories[] = $v->category_id;
                $item = []; 
                $item['id'] = $v->code;
                $item['category_id'] = $v->category_id;
                $item['title'] = $v->title;
                $item["not_available"] = $v->not_available;
                $item['description'] = strip_tags($v->description);
                $item['description'] = str_replace("&nbsp;","", $item['description']);
                $item['description'] = str_replace("&laquo;","", $item['description']);
                $item['description'] = str_replace("&raquo;","", $item['description']);
                $item['description'] = str_replace("&mdash;","", $item['description']);
                $item['description'] = str_replace("&ndash;","", $item['description']);
                $item["price"] = 0;
                $item["link"] = $this->serverName."/shop/product/".$v->slug."-".$v->id; //.Url::to(['/shop/product/view', 'slug' => $v->slug, 'id' => $v->id]);
                //if($item['id'] == "64000-533C-2XL")
                
                
                if($item["not_available"] == "0"){
                    $item["availability"] = "in_stock";
                }else{
                    $item["availability"] = "out_of_stock";
                }
             //   if(!empty($v->images) && is_array($v->images)){
                $item["image_link"] = $this->serverName.$v->getImageFileUrl('image');
                foreach ($v->images as $image){
                    $item['additional_image_link'][] = $this->serverName.$image->getImageFileUrl('image');
                }
                   
            //    }    
                if(!empty($v->size)){
                    // $tmpPrice = "";   
                   
                    foreach($v->size as $skuKey => $skuValue){
                     //  if($skuValue['not_available']< 1){
                      //     $item["sku"][$skuKey]["price"] = $skuValue->attributes["price"];
                     //  }
                        if($item["price"] > $skuValue->attributes["price"] || $item["price"] == 0){
                            $item["price"] = $skuValue->attributes["price"];
                        }
                    }
                    
                }else{
                    $item["price"] = $v->price;   
                }
               
                if(!empty($v->option)){
                    foreach($v->option as $option){
                        if($option->option == "Розмір"){
                            break;
                        }
                        if($option->option == "Стать"){
                            if($option->value == "унісекс"){
                               $item['gender'] = "unisex"; 
                            }elseif($option->value == "для жінок"){
                                $item['gender'] = "female"; 
                            }else{
                                $item['gender'] = "male"; 
                            }
                            
                        }elseif($option->option == "ТМ"){
                            $item['brand'] = $option->value;
                        }elseif($option->option == "Колір"){
                            $item['color'] = $option->value;
                        }else{
                            $item['options'][$option->option] = $option->value ;
                        }
                    }
                }
                if(false){
                    if(!empty($v->size)){
                        // $tmpPrice = "";   

                        foreach($v->size as $skuKey => $skuValue){
                         //  if($skuValue['not_available']< 1){
                          //     $item["sku"][$skuKey]["price"] = $skuValue->attributes["price"];
                         //  }
                            if($item["price"] > $skuValue->attributes["price"] || $item["price"] == 0){
                                $item["price"] = $skuValue->attributes["price"];
                            }
                        }

                    }else{
                        $item["price"] = $v->price;   
                    }
                }else{
                    if(!empty($v->size)){
                        $link = $item["link"];
                        foreach($v->size as $size){
                            $item["size"] = $size->size->name;
                            $item['id'] = $v->code."-".$item["size"];
                            $item["price"] = $size["price"];
                            if($size->not_available == "0"){
                                $item["availability"] = "in_stock";
                            }else{
                                $item["availability"] = "out_of_stock";
                            }
                            $item["link"] = $link."?size=".$size->id;
                            $result[] = $item;

                        }
                    }else{
                        $result[] = $item;
                    }
                }

            }
        }
       // print_r($result[0]);
      //  exit;
        $parents = $map = [];
        $cats = Category::find()->where(["in","id",$categories])->all();
        foreach($cats as $cat){
            if($cat->parent_id != null && !in_array($cat->parent_id,$parents))
                $parents[] = $cat->parent_id;
            $map[$cat->id] = $cat->parent_id;
            
        }

        $catsTranslations = CategoryTranslate::find()->where(["in","shop_category_id",array_merge($categories,$parents)])->where(["language" => "uk"])->all();
        $categoriesName = [];
        foreach($catsTranslations as $cat){
            $categoriesName[$cat["shop_category_id"]] = $cat->title;
        }
        $prudctTypes = [];    
        foreach($map as $catId => $parentId){
       /*     if($parentId == null){
                $prudctTypes[$catId] =$categoriesName[$catId];
            }else{
                $prudctTypes[$catId] = "Головна &gt; ".$categoriesName[$parentId]." &gt; ".$categoriesName[$catId];
            }*/
			$prudctTypes[$catId] = $categoriesName[$catId];
        }
        
        foreach($result as &$product){
            if(isset($prudctTypes[$product['category_id']])){
                $product["product_type"] = $prudctTypes[$product['category_id']];
            }   
        }
      
        return $result;
    }
    function actionIndex(){
		ini_set('memory_limit', '4048M');
        $slugs = $ids = $disbledCatsid = [];
        foreach(Partners::$partners as $slug => $name){
            $slugs[] = $slug;
        }
        $mainCats = Category::find()->where(["in","slug",$slugs])->all();
        
        foreach($mainCats as $cat){
            $ids[] = $cat->id;
        }
        $disbledCats = Category::find()->where(["in","parent_id",$ids])->all();
        foreach($disbledCats as $cat){
            $disbledCatsid[] = $cat->id;
        }
      
        $products= \common\models\Product::find()
                ->where(["=","not_active" ,"0"])
                ->andWhere(["!=","partner","eney"])
                ->andWhere(["not in" , "category_id", $disbledCatsid])
                ->all();
      
        $products = $this->makeArrey($products);
        $serverName = $this->serverName;

        echo count($products);
        echo $this->toFile($this->_render($products, $serverName));
    }
    
    function _render($products,$serverName){
		
         ob_start();
         ?><?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
            <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
            <channel>
                    <title>Vsemerch</title>
                    <link><?=$serverName ?></link>
                <? foreach($products as $prod):?>
                    <item>
                        <g:id><?=$prod["id"]?></g:id>
                        <g:title><?=$prod["title"]?></g:title>
                        <g:description><?=$prod["description"]?> </g:description>
                        <g:link><?=$prod["link"]?></g:link>
                        <?/*<g:ads_redirect>
                            <?=$prod["link"]."?utm_source=googleshoping"?>
                        </g:ads_redirect>
                      */?>
                        <g:image_link><?=$prod["image_link"]?></g:image_link>
                        <? if(isset($prod["additional_image_link"]) && is_array($prod["additional_image_link"])):?>  
                            <? foreach($prod["additional_image_link"] as $k => $image):?>
                                <g:additional_image_link><?=$image?></g:additional_image_link>
                            <? endforeach;?>  
                        <? endif;?>
                        <? if(isset($prod["size"])):?>    
                            <g:size><?=$prod["size"]?></g:size>
                        <? endif;?>
                        <? if(isset($prod["color"])):?>    
                            <g:color><?=$prod["color"]?></g:color>
                        <? endif;?>
                        <? if(isset($prod["brand"])):?>    
                            <g:brand><?=$prod["brand"]?></g:brand>
                        <? endif;?>
                        <? if(isset($prod["gender"])):?>    
                            <g:gender><?=$prod["gender"]?></g:gender>
                        <? endif;?>
                        <g:availability><?=$prod["availability"]?></g:availability>
                        <g:price><?=$prod["price"];?> UAH</g:price>
                        <g:product_type><?=$prod["product_type"];?></g:product_type>
                       <?/* <g:sale_price>1439.00 UAH</g:sale_price> 
                        <g:sale_price_effective_date>2023-02-08/2023-02-28</g:sale_price_effective_date>
                        <g:product_type>
                        товары для детей > игрушки > игрушечные машинки, самолетики, техника
                        </g:product_type>
                        <g:brand>MAY Cheong Group France S.A.S.</g:brand>
                        <g:identifier_exists>no</g:identifier_exists>
                        <g:condition>new</g:condition>
                        <g:color>Белый</g:color>
                        */?>
                        <? if(is_array($prod["options"])):?>
                            <? foreach($prod["options"] as $label => $option):?>
                                <g:product_detail>
                                <g:attribute_name><?=$label?></g:attribute_name>
                                <g:attribute_value><?=$option?></g:attribute_value>
                                </g:product_detail>
                            <? endforeach; ?>
                        <? endif;?>
                    </item>
                <? endforeach;?>
            </channel>
            </rss>
             <?
        return ob_get_clean();
    }
    function toFile($str){
        file_put_contents(\Yii::getAlias('@frontend/web/googleshoping.xml'), $str);
    }
    
}