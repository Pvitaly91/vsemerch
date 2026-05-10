<?php

use \yii\helpers\Html;

use yii\widgets\Pjax;
use yii\helpers\Url;

$title = Yii::t('shop', 'Cart');

/* @var $this yii\web\View */
/* @var $products common\models\Product[] */
?>
<?php Pjax::begin(['enablePushState' => false]); ?>
<?php $not_allowed_str  = ''; ?>
<script>
 
   quantity = document.getElementsByClassName("input_q");
   
   for(k in quantity){
       quantity[k].addEventListener("keydown", function(event) {
        // Allow: backspace, delete, tab, escape, enter, and . (decimal point)
        if ([46, 8, 9, 27, 13, 110].indexOf(event.keyCode) !== -1 ||
            // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Allow: Ctrl+C
            (event.keyCode == 67 && event.ctrlKey === true) ||
            // Allow: Ctrl+X
            (event.keyCode == 88 && event.ctrlKey === true) ||
            // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
             
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) &&
            (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
            
        }else{
            
        }
    });
   }
   /*
    document.getElementById("numericInput").addEventListener("keydown", function(event) {
        // Allow: backspace, delete, tab, escape, enter, and . (decimal point)
        if ([46, 8, 9, 27, 13, 110].indexOf(event.keyCode) !== -1 ||
            // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Allow: Ctrl+C
            (event.keyCode == 67 && event.ctrlKey === true) ||
            // Allow: Ctrl+X
            (event.keyCode == 88 && event.ctrlKey === true) ||
            // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) &&
            (event.keyCode < 96 || event.keyCode > 105)) {
            event.preventDefault();
        }
    });
     * */
    
</script>

<div class="products_card-popup" >
    <?php
   // dd($products);
    if(is_array($products) && count($products)> 0):
        $k = 1;
        foreach ($products as $product): ?>
            <?php
                $product->makeDiscount();
                if($product instanceof \common\models\Product){
                    if(!is_object($product)){
                        continue;
                    }
                   // dd($product);
                    $product = [
                        'url' => Url::to(['/shop/product/view', 'slug' => $product->slug, 'id' => $product->id]),
                        'image' => Html::img($product->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg'), ['class' => 'img-responsive']),
                        'link' => Html::a(Html::encode($product->title), ['/shop/product/view', 'slug' => $product->slug, 'id' => $product->id], ['data-pjax' => 0]),
                        'oldPrice' => $product->oldPrice,
                        'price' => $product->price,
                        'quantity' => $product->getQuantity(),
                        'id' => $product->getId(),
                        'cost' => $product->getCost(),
                        'oldCost' => $product->oldCost,
                        'link_minus' => Html::a('-', ['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1], ['class' => 'btn btn-sm btn-danger', 'disabled' => ($product->getQuantity() - 1) < 1]),
                        'link_plus' => Html::a('+', ['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1], ['class' => 'btn btn-sm btn-success']),
                        'link_remove' => Html::a('×', ['cart/list', 'remove' => 1, 'id' => $product->getId()], [
                            'class' => 'btn btn-danger',
                        ]),
                        '_link_minus' => Url::to(['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1, 'flag' => true]),
                        '_link_plus' => Url::to(['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1, 'flag' => true]),
                        '_link_remove' => Url::to(['cart/list', 'remove' => 1, 'id' => $product->getId(), 'flag' => true]),
                        "input_q" => Url::to(['cart/list', 'update' => 1, 'id' => $product->getId(), 'flag' => true]),
                        "size" => false,
                         'min_q' => $product->getMinQuantity(),
                        ];
                        $id = $product["id"];
                }
                elseif($product instanceof \common\models\ProductSize){
                     $id = $product->getId();
                     if(!is_object($product->product)){
                      
                          \Yii::$app->cart->removeAll();
                     }
                    $product = [
                        'url' => Url::to(['/shop/product/view', 'slug' => $product->product->slug, 'id' => $product->product->id]),
                        'image' => Html::img($product->product->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg'), ['class' => 'img-responsive']),
                        'link' => Html::a(Html::encode($product->product->title . ' ' . $product->size->name), ['/shop/product/view', 'slug' => $product->product->slug, 'id' => $product->product->id], ['data-pjax' => 0]),
                        'price' => $product->price,
                        'oldPrice' => $product->oldPrice,
                        'quantity' => $product->getQuantity(),
                        'id' => $product->getId(),
                        'cost' => $product->getCost(),
                        'oldCost' => $product->oldCost,
                        'link_minus' => Html::a('-', ['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1], ['class' => 'btn btn-sm btn-danger', 'disabled' => ($product->getQuantity() - 1) < 1]),
                        'link_plus' => Html::a('+', ['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1], ['class' => 'btn btn-sm btn-success']),
                        'link_remove' => Html::a('×', ['cart/list', 'remove' => 1, 'size'=>true, 'id' => $product->getId()], [
                            'class' => 'btn btn-danger',
                        ]),
                        '_link_minus' => Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1, 'flag' => true]),
                        '_link_plus' => Url::to(['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1, 'flag' => true]),
                        '_link_remove' => Url::to(['cart/list', 'remove' => 1, 'size'=>true, 'id' => $product->getId(), 'flag' => true]),
                        "input_q" => Url::to(['cart/list', 'update' => 1,  'size'=>true, 'id' => $product->getId(), 'flag' => true]),
                        "size" => true,
                        'min_q' => $product->getMinQuantity(),
                    ];
                   
                }
               
               
                ?>
            <div class="product_card-popup-title-mob item_<?=$id?>">
                <p class = "product_card-popup-title-one" class="product_card-popup-title"><?= $product['link'] ?></p>
              
            </div>
            <div class="product_card-popup item_<?=$id?>" id="item_<?=$id?>"  <? if($product["size"] == true):?>data-size="1"<? endif;?> >
                 
                <div class="product_card-popup-wrapper">
                    <a href="<?= $product['url'] ?>">
                        <?=$product['image']?>
                    </a>
                </div>
                <div class="product_card-popup-header ">
                    <div class="product_card-popup-title ">
                        <p  class="product_card-popup-title product_card-popup-title-one hide-if-mob-popup">
                            <?= $product['link'] ?>
                            
                        </p>
                        <? if($product['min_q'] > 1):?><small style="color:#DB4444"><?= Yii::t('shop', 'min quantity for order') ?> <?=$product['min_q'] ?><?=Yii::t("shop","pcs")?></small><?endif?>
                        <div class="basket-popup">
                            <div>
                                <button class="remove" data-id="<?=$id?>" data-url="<?=$product['_link_remove']?>" >
                                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.2006 5.77081L16.3807 5.87451L16.5813 5.81995L18.3637 5.33503L24.3191 8.77348L24.8048 10.572L24.8586 10.7714L25.0374 10.8746L29.0231 13.1765L28.0651 14.8339L11.2416 5.12535L12.1998 3.46774L16.2006 5.77081ZM9.25 27.7083V10.7083H16.0098L25.75 16.3304V27.7083C25.75 28.3493 25.4954 28.9639 25.0422 29.4172C24.589 29.8704 23.9743 30.125 23.3333 30.125H11.6667C11.0257 30.125 10.411 29.8704 9.95783 29.4172C9.50461 28.9639 9.25 28.3493 9.25 27.7083Z" fill="#DB4444" stroke="#DB4444"/>
                                    </svg>
                                </button>    

                            </div>

                        </div>
                        <?/*
                        <p id = "product_card-popup-title-two" class="product_card-popup-title"><?= Yii::t('shop', 'Quantity') ?>: <strong class = "count_popup" ><?= $quantity = $product['quantity'] ?></strong></p>
                   */?>
                    </div>
                    <div class="add-del-product">
                        <div class="del">

                            <div >
                                 <? /* str_replace(">-<", '> <svg width="35" height="42" viewBox="0 0 35 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="6.5" width="34" height="34" rx="4.5" stroke="#DB4444"/>
                                    <path d="M12.34 25.324V23.056H22.852V25.324H12.34Z" fill="#DB4444"/>
                                </svg><', $product['link_minus'] ) ; */?>
                                <button class="minus" id="minus_<?=$id?>" data-id="<?=$id?>" data-url="<?=$product['_link_minus']?>" <? if($product["size"] == true):?>data-size="true"<? endif;?> data-quantity="<?= $quantity = $product['quantity'] ?>">
                                    <svg width="35" height="42" viewBox="0 0 35 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="0.5" y="6.5" width="34" height="34" rx="4.5" stroke="#DB4444"/>
                                        <path d="M12.34 25.324V23.056H22.852V25.324H12.34Z" fill="#DB4444"/>
                                    </svg>
                                </button>   
                            </div>
                        </div>
                        <input type="text" data-url="<?=$product["input_q"]?>" class="input_q" id="quantity_<?=$id?>" value="<?= $quantity = $product['quantity'] ?>">
                        <div class="add">
                             <div >
                                  <? /*= str_replace(">+<", '><svg width="35" height="42" viewBox="0 0 35 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="4.5" width="34" height="34" rx="4.5" stroke="#DB4444"/>
                                    <path d="M23.068 20.212V22.228H18.604V27.088H16.336V22.228H11.872V20.212H16.336V15.352H18.604V20.212H23.068Z" fill="#DB4444"/>
                                </svg><', $product['link_plus'] ) ; */ ?>
                                 <button class="plus" id="plus_<?=$id?>" data-id="<?=$id?>" data-url="<?=$product['_link_plus']?>" >
                                    <svg width="35" height="42" viewBox="0 0 35 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="0.5" y="4.5" width="34" height="34" rx="4.5" stroke="#DB4444"/>
                                        <path d="M23.068 20.212V22.228H18.604V27.088H16.336V22.228H11.872V20.212H16.336V15.352H18.604V20.212H23.068Z" fill="#DB4444"/>
                                    </svg>
                                 </button>

                            </div>

                        </div>
                    </div>
                    <div class="product_card-popup-price">
                        <p class="product_card-popup-price-one" id="price_<?=$id?>"> <?= Yii::t('shop', 'Price') ?>: <span class="discount" ><? if($product['price'] < $product['oldPrice']):?><?= $product['oldPrice'] ?><? endif;?></span> <span class = "price-popup bold"><?= $product['price'] ?></span> ₴</p>
                        <p class="product_card-popup-price-two"><?= Yii::t('shop', 'Sum') ?>: <span class = "total-price-popup" id="cost_<?=$id?>"> <span class="discount"><? if($product['cost'] < $product['oldCost']):?><?= $product['oldCost'] ?><? endif;?></span> <span class="cost"><?= $product['cost'] ?></span> </span> ₴</p>
                    </div>
                    
                </div>
                  
            </div>
              <? if($k< count($products)):?>
                        <div class="line item_<?=$id?>"></div>
                    <? endif;?>
            <? $k++;?>
        <? endforeach;?> 
                         <div style="margin-bottom: 20px"></div>
    <? else:?>
     <p class = "no_product_card-popup" >
			<?/*Кошик порожній. Додайте товар.*/?>
            <?= Yii::t('shop', 'Empty cart') ?>
            
		</p>
    <? endif;?>
 </div>  <p class="total-price-popup text-min-price <?  if($min_price_msg == false):?>hidden<? endif;?>"><span style=" border-radius:6px; padding: 5px 20px;color:#DB4444; border: 1px solid #DB4444;">Мінімальна сума замовлення <?=$min_order_price?>грн.</span></p>
   
<div class = "order">
	
    <div class="outer-rectangle">
        <div class="btn_price"><p><?= Yii::t('shop', 'Total') ?>: <strong ><span id="total" style="color:white;"><?= \Yii::$app->cart->getCost() ?></span> ₴</strong></p></div>
        <? if(is_array($products) && count($products)> 0):?>
            <div class="btn-to_order">
                <?
          
                if($allow_order == false)
                    $not_allowed_str = "disabled";
                ?>
                <?= Html::a("<p>". Yii::t('shop', 'Оформити замовлення')."</p>", ['cart/order'], [ 'data-pjax' => 0, "class" => "btn-to_order"." ".$not_allowed_str, "id" => "order-button" ]) ?>

            </div>
        <? endif;?>
    </div>

    <?/*<div class="rectangle">
        <div class="wholesale"><p><?= Yii::t('shop', 'OPT from 10 pcs') ?></p></div>
        <div class="price-manager">
            <div class = "call-svg">  
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.90625 26.625C1.46875 26.625 1.10417 26.4792 0.8125 26.1875C0.520833 25.8958 0.375 25.5313 0.375 25.0938V19.1875C0.375 18.8472 0.484375 18.5492 0.703125 18.2935C0.921875 18.0378 1.20139 17.874 1.54167 17.8021L6.57292 16.7812C6.91319 16.7326 7.25979 16.7633 7.61271 16.8731C7.96562 16.983 8.25097 17.1468 8.46875 17.3646L11.8958 20.7917C13.7431 19.6736 15.4323 18.349 16.9635 16.8177C18.4948 15.2865 19.7708 13.6458 20.7917 11.8958L17.2917 8.32292C17.0729 8.10417 16.9329 7.85479 16.8717 7.57479C16.8104 7.29479 16.8046 6.98514 16.8542 6.64583L17.8021 1.54167C17.8507 1.20139 18.0087 0.921875 18.276 0.703125C18.5434 0.484375 18.8472 0.375 19.1875 0.375H25.0938C25.5313 0.375 25.8958 0.520833 26.1875 0.8125C26.4792 1.10417 26.625 1.46875 26.625 1.90625C26.625 5.04167 25.926 8.09833 24.5279 11.0763C23.1299 14.0542 21.2826 16.6913 18.9862 18.9877C16.6899 21.2841 14.0527 23.1313 11.0748 24.5294C8.09688 25.9274 5.04069 26.626 1.90625 26.625Z" fill="#DB4444"/>
                </svg>
            </div>
            <p> <?= Yii::t('shop', 'The price is specified in') ?> <strong><?= Yii::t('shop', 'manager') ?> </strong>
        </div>
        <div class="number-popup_btn"><a href="tel:+380636696886">+38 (063) 6696886</a></div>
    </div>

    <div class="rectangle-mobile">
        <div class="wholesal-mobile"><p><?= Yii::t('shop', 'OPT from 10 pcs') ?></p></div>
        <div class="number-popup_btn-mobile"><a href="tel:+380636696886">+38 (063) 6696886</a></div>
        <div class="price-manager-mobile">
            <div class = "call-svg-mobile">  
                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.90625 26.625C1.46875 26.625 1.10417 26.4792 0.8125 26.1875C0.520833 25.8958 0.375 25.5313 0.375 25.0938V19.1875C0.375 18.8472 0.484375 18.5492 0.703125 18.2935C0.921875 18.0378 1.20139 17.874 1.54167 17.8021L6.57292 16.7812C6.91319 16.7326 7.25979 16.7633 7.61271 16.8731C7.96562 16.983 8.25097 17.1468 8.46875 17.3646L11.8958 20.7917C13.7431 19.6736 15.4323 18.349 16.9635 16.8177C18.4948 15.2865 19.7708 13.6458 20.7917 11.8958L17.2917 8.32292C17.0729 8.10417 16.9329 7.85479 16.8717 7.57479C16.8104 7.29479 16.8046 6.98514 16.8542 6.64583L17.8021 1.54167C17.8507 1.20139 18.0087 0.921875 18.276 0.703125C18.5434 0.484375 18.8472 0.375 19.1875 0.375H25.0938C25.5313 0.375 25.8958 0.520833 26.1875 0.8125C26.4792 1.10417 26.625 1.46875 26.625 1.90625C26.625 5.04167 25.926 8.09833 24.5279 11.0763C23.1299 14.0542 21.2826 16.6913 18.9862 18.9877C16.6899 21.2841 14.0527 23.1313 11.0748 24.5294C8.09688 25.9274 5.04069 26.626 1.90625 26.625Z" fill="#DB4444"/>
                </svg>
            </div>
            <p><?= Yii::t('shop', 'The price is specified in') ?> <strong><?= Yii::t('shop', 'manager') ?></strong>
        </div>
    </div>*/?>
</div>
<?php Pjax::end(); ?>