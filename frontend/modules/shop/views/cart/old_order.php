<?php

use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\ActiveForm;

$title = Yii::t('shop', 'Order');

/* @var $this yii\web\View */
/* @var $products common\models\Product[] */
?>
<? $this->registerCssFile('/css/jquery-ui.css');?>
<? $this->registerJsFile('/js/jquery-ui.js',  ['depends' => [yii\web\JqueryAsset::className()]]); ?>
<?php
$script = '
      
       $(function() {
           
            $(document).mouseup( function(e){ 
                var npDep = $( "#order-npdep" );
                if (!npDep.is(e.target) 
                    && npDep.has(e.target).length === 0  
                    && $("#DepGid").val() == ""){ 
                     npDep.val("");
                }
                var npCity = $( "#order-npcity" );
                if ( !npCity.is(e.target) 
                    && npCity.has(e.target).length === 0  
                    && $("#CityGid").val() == "") { 
                     npCity.val("");
                }
                $(".DeliveryType").change(function(){
                   
                    $("#CityGid").val("");    
                    $("#DepGid").val("");   
                    $("#order-address").val("");  
                    $("#order-npcity").val("");
                    $("#order-npdep").val("");
                    $("#order-address").removeAttr("required");
                    $("#order-npcity").removeAttr("required");
                    $("#order-npdep").removeAttr("required");
                    $(".deliveryBlock").addClass("hide");
                    if($(this).val() == 1){
                        $("#npBlock").removeClass("hide");
                        $("#order-npcity").attr("required","required");
                        $("#order-npdep").attr("required","required");
                    }else if($(this).val() == 2){
                        $("#addressBlock").removeClass("hide");
                        $("#order-address").attr("required","required");
                    }
                        
                });
            });
            
            $( "#order-npcity" ).keyup(function(){
                $("#automplete-2").val("");
                $("#CityGid").val("");
                $("#DepGid").val("");
                $( "#order-npdep" ).val("");
                $("#order-npdep").attr("disabled","disabled");
            });
            $( "#order-npdep" ).keyup(function(){
                $("#DepGid").val("");
            });
         
            $( "#order-npcity" ).autocomplete({
                source: "/api/getCitys",
                minLength: 1,
                select: function( event, ui ) {
                    $("#order-npdep").removeAttr("disabled");
                    $("#CityGid").val(ui.item.key);
                    $.get( "/api/getDepartaments?gid="+ui.item.key, function( data ) {
                            $( "#order-npdep" ).autocomplete({
                            source: data,
                            minLength: 1,
                            select: function( event, ui ) {
                                $("#DepGid").val(ui.item.key);
                            }
                        });
                    });
                
                }
            });
            
         
         });
';

if($order->npDep == NULL){
    $script .= '$("#order-npdep").attr("disabled","disabled");';
}
if($DeliveryType == "1" || $DeliveryType == NULL){
    $script .= ' $("#order-npcity").attr("required","required");
                 $("#order-npdep").attr("required","required");';
}

if($DeliveryType == "2"){
    $script .= '$("#order-address").attr("required","required");';
}
$this->registerJs($script);
?>
<style>
    .ui-autocomplete{
        overflow: auto;
        max-height: 300px;
    }
    .hide{
        display: none;
    }
</style>
<div class="head2">
    <div class="container">
        <div class="info2 col-md-12">
            <h1><?= Html::encode($title) ?></h1>

            <nav class="breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => ['site/index'],
                    ],
                    'links' => [
                        ['label' => Yii::t('shop', 'Shop')],
                        $title
                    ],
                ]) ?>
            </nav>


        </div>
    </div>
</div>
<div class="body_box">
    <div class="top2"></div>
    <div class="content">
        <div class="container cnt">
            <? if(($msg = \Yii::$app->session->getFlash('payment_error_1')) == true):?>     
            <div class="alert alert-danger" role="alert">
              Оплата не прошла: 
                <? if(isset($msg[0])):?>
                    <?= $msg[0];?>
                <? endif;?><br>
                
                
            </div>
            <? endif;?>
             <? if(($msg = \Yii::$app->session->getFlash('payment_error_2')) == true):?>     
            <div class="alert alert-danger" role="alert">
             
                Оплата не прошла, спробуйте ще раз
            </div>
            <? endif;?>
            <div class="order-products">
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
                <div class="cart_item">
                    <div class="row">
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
                                </div>

                            </div>

                        </div>
                        <div class="col-xs-3">
                            <strong><?= $product['cost'] ?> &#8372;</strong>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
                <div class="row">
                    <div class="col-md-2 col-md-offset-5 text-center">
                        <h4><?= Yii::t('shop', 'Total') ?>: <strong><?= $total ?> &#8372;</strong></h4>
                    </div>
                </div>
            </div>
            <hr />
            <div>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 well">
                        <h2><?=Yii::t('shop', 'Contact details')?></h2>
                        <?php
                        /* @var $form ActiveForm */
                        $form = ActiveForm::begin([
                            'id' => 'order-form',
                        ]) ?>

                        <?= $form->field($order, 'name') ?>
                        <?= $form->field($order, 'phone')->widget(\yii\widgets\MaskedInput::className(),[
                            'mask' => '8(999)999-99-99',
                        ]);?>
                        <?= $form->field($order, 'email') ?>
                        
                       
                        <div class="form-group ">
                            <label class="control-label"><?=Yii::t('shop', 'delivery_type')?>:</label><br>
                            <? foreach($deliveryTypes as $id => $label):?>
                                <label class="control-label" ><input type="radio" class="DeliveryType" id="DeliveryType_<?=$id?>" name="DeliveryType" <?=($id== $DeliveryType || ($DeliveryType == NULL && $id == 1) )?'checked="checked"':''?> value="<?=$id?>"> <?=Yii::t('shop', 'delivery_type_'.$id)?></label><br>
                            <? endforeach;?>
                           
                        </div>
                        <? //dd($DeliveryType);?>
                        <div id="npBlock" class="deliveryBlock <? if($DeliveryType != 1 && $DeliveryType != NULL):?>hide<? endif;?>">
                            <?= $form->field($order, 'npCity') ?>
                            <?= $form->field($order, 'npDep') ?>
                           
                            <input type="hidden" name="Order[CityGid]" value="<?=$order->CityGid?>" id = "CityGid">
                            <input type="hidden" name="Order[DepGid]" value="<?=$order->DepGid?>" id = "DepGid">
                        </div> 
                        <div id="addressBlock" class="<? if($DeliveryType != 2):?>hide<? endif;?> deliveryBlock" >
                            <?= $form->field($order, 'address') ?>
                        </div>
                        
                        <?= $form->field($order, 'notes')->textarea(['rows' => 8]) ?>
                     
                       <? if($total >= 500):?>
                            <div class="form-group ">
                                <label class="control-label"><?=Yii::t('shop', 'payment_type')?>:</label><br>
                                <? foreach($paymentsTypes as $id => $label):?>
                                    <label class="control-label" >
                                        <input type="radio" class="PaymentsType" id="PaymentsType_<?=$id?>" name="PaymentType" <?=($id== $PaymentType || ($PaymentType == NULL && $id == 1))?'checked="checked"':''?> value="<?=$id?>"> <?=Yii::t('shop', "PaymentType_".$id)?>
                                    </label><br>
                                <? endforeach?>
                            </div>
                        <? else:?>
                            <input type="hidden" class="PaymentsType" id="PaymentsType_1" name="PaymentType" value="1">
                       <? endif;?>
                       
                       
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <?= Html::submitButton(Yii::t('shop', 'Order'), ['class' => 'btn btn-danger']) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>