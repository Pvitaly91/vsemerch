<?php
namespace common\models\traits;

trait minCountOrder{
    
    
    function getMinQuantity(){
        $partner = $categoryID = NULL;
        if(__CLASS__ == 'common\\models\\ProductSize'){
         
            $prod = \common\models\Product::find()->where(["=","id",$this->product_id])->one(); 
            $categoryID = $prod->category_id;
            $partner = $prod->partner;
        } 
        elseif(__CLASS__ == 'common\\models\\Product'){
         //   dd($this);
            $categoryID = $this->category_id;
            $partner = $this->partner;
        }
      
      //  if(isset(\common\Helpers\Partners::$minQuantity[$partner]) && ($q = \common\Helpers\Partners::$minQuantity[$partner]) == true){
     //       return $q;
     //   }else{
            $category = \common\models\Category::find()->where(["=","id",$categoryID])->one();
            if($category->min_quantity > 0){
                return $category->min_quantity;
            }elseif(isset(\common\Helpers\Partners::$minQuantity[$partner]) && ($q = \common\Helpers\Partners::$minQuantity[$partner]) == true){
                return $q;
            }
            
     //   }
            
       
    }
}