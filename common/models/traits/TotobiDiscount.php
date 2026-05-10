<?php
namespace common\models\traits;

use \common\Helpers\Partners;

trait TotobiDiscount{
    public $discountPrecent = 15;
    public $discountQuantity = 10;
    public $isDiscount = false;
    public $oldCost;
    public $oldPrice;

    function getPartner(){
        if(get_class($this) == "common\models\ProductSize" && ($product = \common\models\Product::find()->where(["id" => $this->product_id])->one()) == true){
            return $product->partner;
        }
        return $this->partner;
    }
    function makeDiscount(){
        $extDiscountProductsCode = ["1909-08"];
     
        if($this->isDiscount == true || in_array($this->code, $extDiscountProductsCode) ){
            return;
        }
    
        $this->oldPrice = $this->price;
        $this->oldCost = $this->getCost();
       // print_r(get_class($this));
        if($this->_quantity >= $this->discountQuantity && $this->getPartner() == Partners::$main){
            $this->price = round($this->price * ((100 - $this->discountPrecent)/100),2);
            $this->isDiscount = true;
        }
        //round($item['price'],2),
    //    dd($this->oldPrice);
    //    dd($this->partner);
    }
    
}
