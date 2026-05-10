<?php

use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\ActiveForm;?>

<div class="products-card_order">
<? $i =1;?>    
<?php foreach ($products as $k => $product): ?>
                     <?php
                $product->makeDiscount();
                if($product instanceof \common\models\Product){
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
                         "size" => false
                        ];
                        $id = $product["id"];
                }
                elseif($product instanceof \common\models\ProductSize){
                     $id = $product->getId();
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
                     
                        "size" => true
                    ];
                   
                }
               
               
                ?>
    <div class="product-card_order">

        <div class="product-card_order_header">
            <h2 id = "product-card_order_name-one" class="product-card_order_name"><?= $product['link'] ?> </h2>

           <?/* <div id = "close-product-card_order-one" class="close-product-card_order">
                <button>
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L16 16" stroke="#989898" stroke-linecap="round"/>
                        <path d="M16 1L1 16" stroke="#989898" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>*/?>

        </div>

        <div class="product-card_order_title">
            <div class="product_card-order-wrapper">
                <a href="<?= $product['url'] ?>">
                   <?=$product['image'];?>
                </a>
            </div>


            <div class="information-about-product-card_order">

                <p class="quantity-product-card_order"><?= Yii::t('shop', 'Quantity') ?>: <span class="quantity-number-product-card_order"><?= $quantity = $product['quantity'] ?></span></p>
               <?/* <p class="description-product-card_order">Колір: <span class="description-number-product-card_order">білий</span></p>*/?>
                <p class="price-product-card_order"><?= Yii::t('shop', 'Price') ?>: <span class="price-number-product-card_order"><?= $product['price'] ?> ₴</span></p>
                <p class="total-price-product-card_order">Сума: <span class="total-price-number-product-card_order"><?= $product['cost'] ?> ₴</span></p>
            </div>
        </div>
        <? if($i < count($products)):?>
        <div class="product-order_line"></div>
        <? else:?>
        <br>
        <? endif;?>
    </div>
    <? $i++;?>
<? endforeach;?>
    


    <div class = "no_product_card-order-product">
        <p>Кошик порожній. Додайте товар.</p>
        <div class = "btn-no_product_card-order-product">
            <button >До магазину</button>
        </div>
    </div>
</div>