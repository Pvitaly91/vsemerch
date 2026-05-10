<?php

namespace common\Helpers;

class Utils{
    static function deactiveCats(){
        $cats = \common\models\Category::find()->where(["<","active","1"])->all();
        foreach($cats as $cat){
            self::deactivProductsByCatid($cat->id);
        }
       
    }
    static function deactivProductsByCatid($catId){
        $products = \common\models\Product::find()->where(["category_id" => $catId, "not_active" => 0])->all();
        if(!empty($products)){
            foreach ($products as $product){
               // dd($product);
                $product->not_active = 1;
                $product->update(false);
            }
        }
        
    }
       static function activProductsByCatid($catId){
        $products = \common\models\Product::find()->where(["category_id" => $catId, "not_active" => 1])->all();
        if(!empty($products)){
            foreach ($products as $product){
               // dd($product);
                $product->not_active = 0;
                $product->update(false);
            }
        }
        
    }
}
