<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

//$this->title = 'Contact';
//$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container_order-pages">
    <? $form = ActiveForm::begin(['action' => ['mail/index'], 'id' => 'contact-form']); ?>

<style>
    .form-control{
    font-weight: 400;
    font-size: 17px;
    line-height: 20px;
    padding: 13px;
    color: #848484;
    text-shadow: 0px 0px 0px #ABABAB;
    -webkit-text-fill-color: transparent;
    padding-left: 20px;
    height: 45px;
    border: 1px solid #C9C9C9;
    border-radius: 7px;
    margin-top: 10px;
}

label.control-label {
    font-weight: 600;
    font-size: 20px;
    line-height: 23px;
    color: #383838;
    margin-top: 25px;
}
.help-block.help-block-error{
    position: absolute;
    font-weight: 400;
    font-size: 10px;
    line-height: 12px;
    color: #DB4444;
    margin-top: 1px;
    margin-left: 21px;
    z-index: -1;
}
</style>
<?// dd($model->getErrors());?>
    <div class="order-product-information_block">
        <div class="order-block-information">
            <div class="order-information_input">
                <div class="contact-infotmation_input">
                    <div class="contact-infotmation_input-text">
                        <p class="contact-infotmation_h1"><?= Yii::t('app', 'Mail send') ?></p>
                        <p class="contact-infotmation_h2">Ім'я</p>
                        <input type="text"  class="input-name"  name="Mail[name]" value="<?=$model->name?>" placeholder="Введіть ім'я" >
                        <? if(isset($model->getErrors("name")[0]) && ($error = $model->getErrors("name")[0]) == true):?>
                            <p  class="error-message"><?=$error?></p>
                         <? endif;?>

                        <p class="contact-infotmation_h2">Email</p>
                        <input type="email" id="email-input" class="input-name" name="Mail[email]" value="<?=$model->email?>"  placeholder="Введіть email" >
                       <? if(isset($model->getErrors("email")[0]) && ($error = $model->getErrors("email")[0]) == true):?>
                            <p  class="error-message"><?=$error?></p>
                         <? endif;?>
                        <? /*
                          <p class="contact-infotmation_h2">Телефон</p>
                          <input class="input-name" type="tel" id="phone"  name="Mail[phone]" pattern="\+380 \([0-9]{2}\) [0-9]{2} [0-9]{2} [0-9]{3}" required placeholder="+380 (__) __ __ ___">
                          <p id="phone-error" class="error-message"></p> */ ?>

                        <p class="contact-infotmation_h2">Тема</p>
                        <input class="input-name" type="tel" id="phone"  name="Mail[subject]"  value="<?=$model->subject?>"   placeholder="Введіть тему">
                        <? if(isset($model->getErrors("subject")[0]) && ($error = $model->getErrors("subject")[0]) == true):?>
                            <p  class="error-message"><?=$error?></p>
                         <? endif;?>

                        <p class="contact-infotmation_h2">Повідомлення</p>
                        <textarea name="comment" name="Mail[body]" id="input-name_delivery-method-comment"><?=$model->body?></textarea>
                        <? if(isset($model->getErrors("body")[0]) && ($error = $model->getErrors("body")[0]) == true):?>
                            <p  class="error-message"><?=$error?></p>
                         <? endif;?>
                        <?= $form->field($model, 'verifyCode')->widget(Captcha::className()) ?>
                        <div class="to-order-products-card-order_btn">
                            <button id="to-order-products-card-order_btn-id"><?=Yii::t('app', 'Submit send')?></button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
   <?php ActiveForm::end(); ?> 
</div>

