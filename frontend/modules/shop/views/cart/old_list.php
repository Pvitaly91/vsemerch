<?php

use \yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use yii\helpers\Url;

$title = Yii::t('shop', 'Cart');

/* @var $this yii\web\View */
/* @var $products common\models\Product[] */
?>
<?php Pjax::begin(['enablePushState' => false]); ?>

    <div class="">
        <div class="row cart_list_begin">
            <div class="col-xs-9">
                <div class="col-xs-3">
                </div>
                <div class="col-xs-9">
                    <?= Yii::t('shop', 'Product') ?>
                </div>
            </div>
            <div class="col-xs-3">
                <?= Yii::t('shop', 'Cost') ?>
            </div>
        </div>
        <?php foreach ($products as $product): ?>
        <?php
            if($product instanceof \common\models\Product){
                $product = [
                    'url' => Url::to(['/shop/product/view', 'slug' => $product->slug, 'id' => $product->id]),
                    'image' => Html::img($product->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg'), ['class' => 'img-responsive']),
                    'link' => Html::a(Html::encode($product->title), ['/shop/product/view', 'slug' => $product->slug, 'id' => $product->id], ['data-pjax' => 0]),
                    'price' => $product->price,
                    'quantity' => $product->getQuantity(),
                    'id' => $product->getId(),
                    'cost' => $product->getCost(),
                    'link_minus' => Html::a('-', ['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1], ['class' => 'btn btn-sm btn-danger', 'disabled' => ($product->getQuantity() - 1) < 1]),
                    'link_plus' => Html::a('+', ['cart/list', 'update' => 1, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1], ['class' => 'btn btn-sm btn-success']),
                    'link_remove' => Html::a('×', ['cart/list', 'remove' => 1, 'id' => $product->getId()], [
                        'class' => 'btn btn-danger',
                    ]),
                    ];
            }
            elseif($product instanceof \common\models\ProductSize){
                $product = [
                    'url' => Url::to(['/shop/product/view', 'slug' => $product->product->slug, 'id' => $product->product->id]),
                    'image' => Html::img($product->product->getThumbFileUrl('image', 'thumb', '/img/no_image.jpg'), ['class' => 'img-responsive']),
                    'link' => Html::a(Html::encode($product->product->title . ' ' . $product->size->name), ['/shop/product/view', 'slug' => $product->product->slug, 'id' => $product->product->id], ['data-pjax' => 0]),
                    'price' => $product->price,
                    'quantity' => $product->getQuantity(),
                    'id' => $product->getId(),
                    'cost' => $product->getCost(),
                    'link_minus' => Html::a('-', ['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() - 1], ['class' => 'btn btn-sm btn-danger', 'disabled' => ($product->getQuantity() - 1) < 1]),
                    'link_plus' => Html::a('+', ['cart/list', 'update' => 1, 'size'=>true, 'id' => $product->getId(), 'quantity' => $product->getQuantity() + 1], ['class' => 'btn btn-sm btn-success']),
                    'link_remove' => Html::a('×', ['cart/list', 'remove' => 1, 'size'=>true, 'id' => $product->getId()], [
                        'class' => 'btn btn-danger',
                    ]),
                ];
            }
            ?>
            <div class="row cart_item">
                <div class="col-xs-9">
                    <div class="col-xs-3">
                        <a href="<?= $product['url'] ?>"
                           data-pjax="0">
                            <?php

                            echo $product['image'];

                            ?>
                        </a>
                    </div>
                    <div class="col-xs-9">
                        <h4><?= $product['link'] ?></h4>

                        <div>
                            <?= Yii::t('shop', 'Price') ?>:
                            <strong><?= $product['price'] ?> &#8372;</strong>
                        </div>
                        <div>
                            <?= Yii::t('shop', 'Quantity') ?>:
                            <strong><?= $quantity = $product['quantity'] ?></strong>

                            <?= $product['link_minus'] ?>
                            <?= $product['link_plus'] ?>
                        </div>

                    </div>

                </div>
                <div class="col-xs-3">
                    <strong><?= $product['cost'] ?> &#8372;</strong>
                    <?= $product['link_remove'] ?>
                </div>
            </div>

        <?php endforeach ?>
        <div class="row">
            <div class="col-md-2 col-md-offset-5 text-center">
                <h4><?= Yii::t('shop', 'Total') ?>: <strong><?= $total ?> &#8372;</strong></h4>
                <?= Html::a(Yii::t('shop', 'Order submit'), ['cart/order'], ['class' => 'btn btn-danger', 'data-pjax' => 0]) ?>
            </div>
        </div>
    </div>
<?php Pjax::end(); ?>