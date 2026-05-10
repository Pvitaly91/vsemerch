<?php

namespace frontend\modules\shop\controllers;

use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use common\models\ProductSize;
use common\models\ProductTranslate;
use yz\shoppingcart\ShoppingCart;
use yii\helpers\Url;
use yii\web\HttpException;

class CartController extends \yii\web\Controller
{
    public $minOrderPrice = 500;
    public $deliveryTypes = [
            1 => "Відділення Нової Пошти",
            2 => "Адресна доставка",
            3 => "Самовивоз",
        ];
    public $paymentsTypes = [
            1 => "Післяплата",
          //  2 => "Сплатити картою",
        ];
    public function actionItemsInCart()
    {
        return \Yii::$app->cart->getCount();
    }
    function addToCart($id, $size_id = null){
        if ($size_id) {
            $query = ProductSize::find()->where([ProductSize::tableName() . '.id' => $size_id])->andWhere([ProductSize::tableName() . '.product_id' => $id]);
        } else {
            $query = Product::find()->where([Product::tableName() . '.id' => $id]);
        }
        
        $product = $query->one();

        if ($product) {
            \Yii::$app->cart->put($product);
            //\Yii::$app->session->setFlash('growl', 'Товар добавлен в корзину!');
            //return $this->goBack();
        }
    }
    public function actionAdd($id, $size_id = null)
    {
        $this->addToCart($id, $size_id);
    }

    public function actionList()
    { 
        $q = (\Yii::$app->request->get('quantity') <1)?1:\Yii::$app->request->get('quantity');
        if (\Yii::$app->request->get('update') && \Yii::$app->request->get('id')) {
          
            if(\Yii::$app->request->get('size')) {
                $product = ProductSize::findOne(\Yii::$app->request->get('id'));
            } else {
                $product = Product::findOne(\Yii::$app->request->get('id'));
            }
            if ($product) {
              //  echo \Yii::$app->request->get('quantity');
            //    exit;
                \Yii::$app->cart->update($product, $q);
            }
            if(\Yii::$app->request->get('falg')){
                return;
            }
        }
        // \Yii::$app->cart->update($product, 30);
        if (\Yii::$app->request->get('remove') && \Yii::$app->request->get('id')) {
            if(\Yii::$app->request->get('size')) {
                $product = ProductSize::findOne(\Yii::$app->request->get('id'));
            } else {
                $product = Product::findOne(\Yii::$app->request->get('id'));
            }
            if ($product) {
                \Yii::$app->cart->remove($product);
            }
            if(\Yii::$app->request->get('falg')){
                return;
            }
        }
        /* @var $cart ShoppingCart */
        $cart = \Yii::$app->cart;

        $products = $cart->getPositions();
      
        $total = round($cart->getCost(),2);
        $q_allow_price = true;
        $result = [
            'allow_order' => false,
            'min_price_msg' => false,
        ];
        //2587
        foreach($products as $id => $item){
           
            if($item["quantity"] < $item->getMinQuantity() ){
                $q_allow_price = false;
                break;
            }  
                
        }
        
        $result["min_price_msg"] = $total < $this->minOrderPrice;
        if($q_allow_price == true) {
            $result["allow_order"] = $total > $this->minOrderPrice;
        }

        if(isset($_GET["flag"]) && is_array($products)){

            $result = [
                'allow_order' => false,
                'min_price_msg' => false,
            ];
            //   '_link_minus' => Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1, 'flag' => true]),
            //            '_link_plus' => Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1, 'flag' => true]),
            $total = 0;        
            foreach($products as $id => $item){
               
                $item->makeDiscount();
              $cost = round($item['cost'],2);
                $result["items"][$id] = [
                    "isDiscount" => $item->isDiscount,
                    "price" => round($item['price'],2),
                    "oldPrice" => round($item->oldPrice,2),
                    "oldCost" => round($item->oldCost,2),
                    "cost" => $cost,
                    "quantity" => $item["quantity"],
                    
                ];
                $total += $cost;
                if($item instanceof \common\models\Product){
                    $result["items"][$id]['plus'] = Url::to(['cart/list', 'update' => 1, 'id' => $item->getId(), 'quantity' => $item->getQuantity() + 1, 'flag' => true]);
                    $result["items"][$id]['minus'] = Url::to(['cart/list', 'update' => 1, 'id' => $item->getId(), 'quantity' => $item->getQuantity() - 1, 'flag' => true]);
                   
                }elseif($item instanceof \common\models\ProductSize){
                   
                    $result["items"][$id]['plus'] = Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $item->getId(), 'quantity' => $item->getQuantity() + 1, 'flag' => true]);
                    $result["items"][$id]['minus'] = Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $item->getId(), 'quantity' => $item->getQuantity() - 1, 'flag' => true]);
               
                   
                }
                
            }
            
            $result["total"] = round($total,2);
           
            $result["min_price_msg"] = $result["total"] < $this->minOrderPrice;
            if($q_allow_price == true) {
                $result["allow_order"] = $result["total"] > $this->minOrderPrice;
            }
            $result["count"] = \Yii::$app->cart->getCount();
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit;
        }
        return $this->renderAjax('list', [
            'products' => $products,
            'total' => $cart->getCost(),
            "allow_order" => $result["allow_order"],
            "min_order_price" => $this->minOrderPrice,
            "min_price_msg"  => $result["min_price_msg"]
        ]);
    }

    public function actionRemove($id, $flag=false)
    {
        if(\Yii::$app->request->get('size')) {
            $product = ProductSize::findOne($id);
        } else {
            $product = Product::findOne($id);
        }
       if ($product) {
            \Yii::$app->cart->remove($product);
            if($flag == false)
                $this->redirect(['cart/list']);
        }
    }

    public function actionUpdate($id, $quantity, $flag=false)
    {
        $product = Product::findOne($id);
        if ($product) {
            \Yii::$app->cart->update($product, $quantity);
            if($flag = false)
                $this->redirect(['cart/list']);
        }
    }
    public function setOrder($post,$products,$gProducts = false){
        //ALTER TABLE `shop_order` ADD `payment` text COLLATE 'utf8_unicode_ci' NULL AFTER `notes`;
       
        $order = new Order();
        //$products = \Yii::$app->cart->getPositions();
       //$products = unserialize($_SESSION["order_post"]["cart"]);
  
        if ($order->load($post) && $order->validate()) {
            $transaction = $order->getDb()->beginTransaction();
            $order->save(false);
            
            $gProducts["orderId"] = $order->id;
            
            foreach ($products as $product) {
                $product->makeDiscount();
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->code = $product->code;
                if($product instanceof ProductSize) {
                    $orderItem->title = $product->product->title . ' ' . $product->size->name;
                    $orderItem->product_id = $product->product->id;
                } else {
                    $orderItem->title = $product->title;
                    $orderItem->product_id = $product->id;
                }
                $orderItem->price = $product->getPrice();
                $orderItem->quantity = $product->getQuantity();
                if (!$orderItem->save(false)) {
                    $transaction->rollBack();
                    \Yii::$app->session->addFlash('error', 'Cannot place your order. Please contact us.');
                    return $this->redirect('catalog/list');
                }
            }

            $transaction->commit();
            \Yii::$app->cart->removeAll();
            \Yii::$app->session->addFlash('gdm',$gProducts);
            \Yii::$app->session->addFlash('success', \Yii::t('shop', 'Thanks for your order. We\'ll contact you soon.'));
            if(isset($_SESSION["order_post"])){
                unset($_SESSION["order_post"]);
            }
            if(IS_LOCAL == false)
                $order->sendEmail();
                        
            return true;
        }
    }
    public function serializeCart(){
        $products = \Yii::$app->cart->getPositions();
        $cartProctsIds = [];
        foreach ($products as $id => $item){
            for($i=1;$i <= $item->getQuantity(); $i++)
            {
                 if($item instanceof ProductSize) {
                //    $orderItem->title = $item->product->title . ' ' . $product->size->name;
            // d($item->size);
                    $cartProctsIds[] = ["id" => $item->product->id, "size" => $item->size->id];
                } else {
                    $cartProctsIds[] = ["id" => $item->id, "size" => ""];
                    
                }
                
                
            }
        }
        return $cartProctsIds;
    }
    public function restoreCart(array $serializedCart){
        foreach($serializedCart as $item){
            $this->addToCart($item["id"],$item["size"]);
        }
    }
    public function actionOrder()
    {
       
     //   $item = \Yii::$app->cart->getPositions()[1400];
    //  $item->price = 200;
       // $item->makeDiscount();
      //  dd($item->getQuantity());
      //  dd($item->getCost());
      // dd($item->getPrice());
     //   dd(\Yii::$app->cart->getCost());
       
       //  dd();
      //  dd(count(\Yii::$app->cart->getPositions()));
        $order = new Order();
   
        /* @var $cart ShoppingCart */
        $cart = \Yii::$app->cart;
       
        /* @var $products Product[] */
        $products = $cart->getPositions();
       
       
        $gProducts = [];
        foreach ($products as $items){
            $items->makeDiscount();
            $gProducts["PRODUCT_LIST"][] = ["id" => $items->code, "google_business_vertical" => 'retail'];
        }
         $total = round($cart->getCost(),2);
         
        if($total < $this->minOrderPrice)
            throw new HttpException(404, 'Данной странице не существует!');   
        $gProducts["total"] = $total;
     // dd($gProducts);
       // $d = $cart->getSerialized();
      //  $cart->removeAll();
     //   $cart->setSerialized($d);
     //   dd($cart->getPositions());
      // dd($_SESSION);
     //   $this->restoreCart($d);
        
        if(($post = \Yii::$app->request->post()) == true ){
            $order->setAttributes($post["Order"]);
            if($order->validate()){
                $_SESSION["order_post"]["order_post_form"] = $post;
                if($post["DeliveryType"] == 1){

                    if(!empty($post["Order"]["npCity"]))
                        $post["Order"]["address"] =  $this->deliveryTypes[$post["DeliveryType"]].": ".$post["Order"]["npCity"];

                    if(!empty($post["Order"]["npDep"]))
                        $post["Order"]["address"] .=  ", ".$post["Order"]["npDep"];

                }elseif($post["DeliveryType"] == 2){
                   $post["Order"]["address"] = $this->deliveryTypes[$post["DeliveryType"]].": ".$post["Order"]["address"]; 
                }elseif($post["DeliveryType"] == 3){
                     $post["Order"]["address"] = $this->deliveryTypes[$post["DeliveryType"]];
                }  

                unset($post["Order"]["npCity"]);
                unset($post["Order"]["npDep"]);
                $_SESSION["order_post"]["Order"] = $post["Order"];
                $_SESSION["order_post"]["cart"] = $cart->getSerialized();

                if($post["PaymentType"] == "2"){
                    return $this->redirect('/wayforpay');
                }elseif(((isset($post["PaymentType"]) && $post["PaymentType"] == "1") || !isset($post["PaymentType"])) && $this->setOrder($post,$products,$gProducts) == true){

                    return $this->redirect('success');
                }
            }    
        }
 
      
        $DeliveryType = null;
        $PaymentType = null;

        if(isset($_SESSION["order_post"]["order_post_form"]["Order"])){
             $order->setAttributes($_SESSION["order_post"]["order_post_form"]["Order"]);
        }
        if(isset($_SESSION["order_post"]["order_post_form"]["DeliveryType"])){
           $DeliveryType = $_SESSION["order_post"]["order_post_form"]["DeliveryType"];
        }
        if(isset($_SESSION["order_post"]["order_post_form"]["PaymentType"])){
           $PaymentType = $_SESSION["order_post"]["order_post_form"]["PaymentType"];
        }
  
   //     dd($DeliveryType);
        return $this->render('order', [
            'order' => $order,
            'products' => $products,
            'total' => $total,
            "DeliveryType" => $DeliveryType,
             "PaymentType" => $PaymentType,
            "deliveryTypes" => $this->deliveryTypes,
            "paymentsTypes" => $this->paymentsTypes    
        ]);
    }

    public function actionSuccess()
    { 
       
      //\Yii::$app->session->addFlash('success', \Yii::t('shop', 'Thanks for your order. We\'ll contact you soon.'));
        if(($orderPost = \Yii::$app->session->get("order_post")) == true && isset($orderPost["cart"]) ){
            $orderPost["Order"]["payment"] = "2";    
            $this->setOrder($orderPost,unserialize ($orderPost["cart"]));  
            
        }
        $success = \Yii::$app->session->getFlash('success');
      
        if(empty($success) ){
            return $this->redirect(Url::to(["/site/index"]));
        }
        return $this->render('success',["success" => $success]);
    }
}
