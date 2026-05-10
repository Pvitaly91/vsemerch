<div class="delivery-method_input">
    <div class="delivery-method_input-text">
        <? if (true && $total >= 500): ?>
   
            <div class="payments">
                <p class="contact-infotmation_h1"><?= Yii::t('shop', 'payment_type') ?></p>
                <div class = "radio_btn-order">
                    <? foreach ($paymentsTypes as $id => $label): ?>
                        <div class="radio_btn_delivery-method-block">
                            <input type="radio" id="PaymentsType_<?= $id ?>" name="PaymentType" <?= ($id == $PaymentType || ($PaymentType == NULL && $id == 1)) ? 'checked="checked"' : '' ?> value="<?= $id ?>"> 
                            <label class="delivery-method_radio_btn" for="PaymentsType_<?= $id ?>" ><?= Yii::t('shop', "PaymentType_" . $id) ?></label>
                        </div>  
                    <? endforeach ?>
                </div> 
            </div>    
        <? else: ?>
            <input type="hidden" class="PaymentsType" id="PaymentsType_1" name="PaymentType" value="1">
        <? endif; ?>
        <p class="contact-infotmation_h1"><?=Yii::t('shop', 'delivery_type')?></p>

        <div class = "radio_btn-order">
            <? foreach ($deliveryTypes as $id => $label): ?>
                <div class="radio_btn_delivery-method-block">
                    <input type="radio" class="DeliveryType" id="DeliveryType_<?= $id ?>" name="DeliveryType" <?= ($id == $DeliveryType || ($DeliveryType == NULL && $id == 1) ) ? 'checked="checked"' : '' ?> value="<?= $id ?>"> 
                    <label class="delivery-method_radio_btn" for="DeliveryType_<?= $id ?>" ><?= Yii::t('shop', 'delivery_type_' . $id) ?>  </label>     
                </div>     
            <? endforeach; ?>
        </div>


        <div class="delivery-method_information_input " >
            <? //=============NP start ============// ?>
            <div id="npBlock" class="deliveryBlock <? if ($DeliveryType != 1 && $DeliveryType != NULL): ?>hide<? endif; ?>">
                <input type="hidden" name="Order[CityGid]" value="<?= $order->CityGid ?>" id = "CityGid">
                <input type="hidden" name="Order[DepGid]" value="<?= $order->DepGid ?>" id = "DepGid">
                <p class="delivery-method_btn"><?=Yii::t('shop', 'npCity')?></p>
                <input id = "order-npcity" name="Order[npCity]" value="<?= $order->npCity ?>" class="input-name_delivery-method" type="text" placeholder="<?=Yii::t('shop', 'Input')?> <?=Yii::t('shop', 'npCity')?>">

                <? if (isset($order->getErrors("npCity")[0]) && ($error = $order->getErrors("npCity")[0]) == true): ?>
                    <p id="city-error" class="error-message"><?= $error ?></p>
                <? endif; ?>

                <p class="delivery-method_btn"><?=Yii::t('shop', 'npDep')?></p>
                <input id = "order-npdep" class="input-name_delivery-method" name="Order[npDep]"  value="<?= $order->npDep ?>" type="text" placeholder="<?=Yii::t('shop', 'Input')?> <?=Yii::t('shop', 'Number or Address')?>">
                <? if (isset($order->getErrors("npDep")[0]) && ($error = $order->getErrors("npDep")[0]) == true): ?>
                    <p id="department-error" class="error-message"><?= $error ?></p>
                <? endif; ?>
                        
            </div>
            <? //=============NP end ============// ?>
            <div id="addressBlock" class="<? if ($DeliveryType != 2): ?>hide<? endif; ?> deliveryBlock" >
                <p class="delivery-method_btn"><?=Yii::t('shop', 'Address')?></p>
                <input type="text" id="order-address" class="input-name_delivery-method" value="<?= $order->address ?>" name="Order[address]" >
            </div>
            <p class="delivery-method_btn"><?=Yii::t('shop', 'Notes')?></p>
            <textarea name="Order[notes]"  id="input-name_delivery-method-comment"><?= $order->notes ?></textarea>


        </div>

    </div>
</div>