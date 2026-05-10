<?php

use \yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\ActiveForm;

$title = Yii::t('shop', 'Order');

/* @var $this yii\web\View */
/* @var $products common\models\Product[] */
$this->registerCssFile('/new/css/styles.css');

?>

<? $this->registerCssFile('/css/jquery-ui.css');?>
<? $this->registerJsFile('/js/jquery-ui.js',  ['depends' => [yii\web\JqueryAsset::className()]]); 
 
$this->registerCssFile('/new/css/order-product.css');
/*
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<?php
 * 
 */?>
    <script src="/js/_jquery-11.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
     <?
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

  <!-- Include jQuery Inputmask plugin -->

<script>
    $(document).ready(function(){
        $(".form-submit").click(function(){
            $("#submit-form").trigger("click");
        });
       
        $('input.form-tel-masked').inputmask({
			mask: "8(*{3})*{7}",
			"placeholder": "8(___)_______",
			definitions: {
				'*': {
					validator: "[0-9]",
				}
			}
        });
 
    });
    
</script>
 

<div class="container_order-pages">
			<div class="order-product-information_block">
               
				<div class="order-block-information">
					<h2 class="placing_an_order"><?= Html::encode($title) ?></h2>
                    <?php
                        /* @var $form ActiveForm */
                        $form = ActiveForm::begin([
                            'id' => 'order-form',
                        ]) ?>
                    <div class="order-information_input">
                        <div class="contact-infotmation_input">
                            <div class="contact-infotmation_input-text">
                                <p class="contact-infotmation_h1"><?=Yii::t('shop', 'Contact details')?></p>
                                
                                <p class="contact-infotmation_h2"><?=Yii::t('shop', 'FIO')?></p>
                                <input class="input-name" name="Order[name]" value="<?=$order->name?>" id = "name-input" type="text" placeholder="<?=Yii::t('shop', 'Input')?> <?=Yii::t('shop', 'FIO')?>" >
                                <? if(isset($order->getErrors("name")[0]) && ($error = $order->getErrors("name")[0]) == true):?>
                                    <p id="name-error" class="error-message"><?=$error?></p>
                                <? endif;?>
                                <p class="contact-infotmation_h2"><?=Yii::t('shop', 'Email')?></p>
                                <input type="email" name="Order[email]" value="<?=$order->email?>" id="email-input" class="input-name" placeholder="<?=Yii::t('shop', 'Input')?> <?=Yii::t('shop', 'Email')?>" >
                                <? if(isset($order->getErrors("email")[0]) && ($error = $order->getErrors("email")[0]) == true):?>
                                    <p id="email-error" class="error-message"><?=$error?></p>
                                <? endif;?>
                                <p class="contact-infotmation_h2"><?=Yii::t('shop', 'Phone')?></p>
                                <input class="input-name form-tel-masked" data-plugin-inputmask="inputmask_9b456e36" name="Order[phone]" value="<?=$order->phone?>" type="tel" id="phone" placeholder="8(___)_______" <?/*pattern="\+380 \([0-9]{2}\) [0-9]{2} [0-9]{2} [0-9]{3}"  */?> >
                                <? if(isset($order->getErrors("phone")[0]) && ($error = $order->getErrors("phone")[0]) == true):?>
                                    <p id="phone-error" class="error-message"><?=$error?></p>
                                <? endif;?>
                            </div>
                        </div>
                    </div>

                    <? include "delivery_block.php"?>
                    
                    <input type="submit" id="submit-form" value="submit" style="display:none;">
                    <?php ActiveForm::end() ?>
				</div>
               
               
				<div class="products-card_order-block">
					<? include 'product_list.php';?>
                    
					<div class="product-card_order-total_price-btn hide-if-mob">
						<p class="total-price-products-order_btn-text"><?= Yii::t('shop', 'Total') ?>: <span class="total-price-products-order_btn-text-number"><?= \Yii::$app->cart->getCost() ?> ₴</span></p>
						<div class="to-order-products-card-order_btn">
						<button id = "to-order-products-card-order_btn-id" class="form-submit"><?=Yii::t('shop', 'Order')?></button>
						</div>
					</div>
	

				</div>
				
                
			</div>
            <div class="product-card_order-total_price-btn show-if-mob">
						<p class="total-price-products-order_btn-text"><?= Yii::t('shop', 'Total') ?>: <span class="total-price-products-order_btn-text-number"><?= \Yii::$app->cart->getCost() ?> ₴</span></p>
						<div class="to-order-products-card-order_btn">
						<button id = "to-order-products-card-order_btn-id" class="form-submit"><?=Yii::t('shop', 'Order')?></button>
						</div>
			</div>
		</div>