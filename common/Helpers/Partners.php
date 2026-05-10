<?php
namespace common\Helpers;

use common\models\Category;

class Partners{
    public static $main= "totobi";
    public static $_main = ["totobi","main"];
    public static $partners = [ 
            "eney" => "Eney",
            "bergamo" => "Bergamo",
            "toptime" => "toptime",
            "esuvenir" => "E-suvenir",
        ];
    public static $disabled =  [
        "eney" => false,
        "bergamo" => false,
        "toptime" => false,
        "esuvenir"=> false,
    ];
    public static $minQuantity = ["eney" => "20","toptime" => "20"];
    public static $ext = [
            "eney" => [
                   "freightpacking",
                   "moq",
                   "weight_with_packing",
                   "individual_packaging",
                    "store"
            ]
        ];
    static function test(){
       
    }
    public static function isAdmin(){
        $adminUsers[] = 2;
        $result = false;
        if(in_array(\Yii::$app->User->GetId(), $adminUsers))
            $result =  true;
        
        return $result;
    }
    
    static function disabled($partner,$model = false){
        $result = false;
        if(isset(self::$partners[$partner]) && isset(self::$disabled[$partner])){
            $result = self::$disabled[$partner];
            if(self::isAdmin()){
                $result = false;
            }
        }
        if(is_object($model) && $model->slug == $model->partner){
            $result = true;
            if(self::isAdmin())
                $result = false;
        }  
        return $result;
    }
    static function getPrefixAdmin($partner){
        if(self::isAdmin() && isset(self::$partners[$partner])){
            return strtoupper(self::$partners[$partner])." ";
        }
    }
    // only for Eney partner
    static function availableProp($not_available,&$attributes){
         if($not_available == 1){
            $attributes["uk"]["Наявність"] = "Під замовлення";
            $attributes["ru"]["Наличие"] = "Под заказ";
            $attributes["en"]["Availability"] = "To order";
        }elseif($not_available == 0){
            $attributes["uk"]["Наявність"] = "В наявності";
            $attributes["ru"]["Наличие"] = "В наличии";
            $attributes["en"]["Availability"] = "In stock";
        }
    }
    static function isMain($partner){
        if($partner == "" || self::$main == $partner){
            return true;
        }else{
            return false;
        }
        
    }
    static function getMainCategoryBySlug($slug){
             
        return Category::find()
                ->where(["slug" => $slug])                    
                ->orWhere(["is", "partner", NULL])
                ->andWhere(["partner" => "totobi"])
                ->all();
        
    }
}