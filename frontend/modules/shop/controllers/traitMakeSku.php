<?php
namespace frontend\modules\shop\controllers;

use common\models\Product;
use yii\helpers\Url;

trait traitMakeSku{
    public function makeGallery($item, $listOnly = false){
        $sku = [];
        $thumbUrl = $item->getLazyPic('thumb');
        $sku["link"] = Url::to(['/shop/product/view', 'slug' => $item->slug, 'id' => $item->id]);
        $sku["previewImg"]["ico"] = $thumbUrl;
        $sku["previewImg"]["thumb"] = $thumbUrl;
        $sku["previewImg"]["preview"] = $listOnly ? $thumbUrl : $item->getLazyPic('preview');
        $sku["previewImg"]["image"] = $listOnly ? $thumbUrl : $item->getLazyPic('original');
        $sku["morePhode"][] = $sku["previewImg"];
        if ($listOnly) {
            return $sku;
        }

        foreach($item->getGalleryImages() as $image){

            $img["ico"] = $image->getLazyPic('ico');
            $img["image"] = $image->getLazyPic('original');
            $img["thumb"] = $image->getLazyPic('thumb');
            $sku["morePhode"][] = $img;
        }
        return $sku;
    }
    
    public function makeSKU(&$model,&$canonical = false, $listOnly = false){
      //  $except = ["60430"];
      
      //  if(in_array($model->sku_group,$except)){
     //      return; 
     //   }

        if(isset(\common\Helpers\Partners::$partners[$model->partner]) && \common\Helpers\Partners::$partners[$model->partner]){
             $skuGroup =  $model->sku_group;
              $models = Product::find()
               // ->where(['like',"code",$codeParts[0]."-%",false] )    
                ->where(['=',"sku_group",$skuGroup] )  
                ->andWhere(["partner" => $model->partner])      
                ->andWhere(["not_active" => 0])   
                ->all();
        }else{
            $codeParts = explode("-",$model->code);
            $skuGroup =  $codeParts[0];
             $models = Product::find()
               // ->where(['like',"code",$codeParts[0]."-%",false] )    
                ->where(['=',"sku_group",$skuGroup] )  
                ->andWhere(['slug' => $model->slug,"not_active" => 0])   
                ->all();
        }
       
      
       
        
       // dd(count($models));
        $skus = [];
        foreach ($models as $item ){
            $sku = $this->makeGallery($item, $listOnly);
            if($item->is_main == "1"){
                $canonical =  "https://".$_SERVER["SERVER_NAME"].Url::to(['/shop/product/view', 'slug' => $item->slug, 'id' => $item->id]);
            }
            $item->checkAvaiableSize();
            $sku["model"] = $item;
            
            if($item->size != NULL && is_array($item->size) && $model->price_old == NULL){
                $price = NULL;

                foreach($item->size as $sizeItem){
                    if(($sizeItem->price > 0 && $price === NULL) || $sizeItem->price < $price){
                        $price = $sizeItem->price;
                    }
                }
                $model->price_old = $model->price = $price;
            }
            $skus[] = $sku;
        }
       // dd($skus);
        return $skus;
    }
   
}
