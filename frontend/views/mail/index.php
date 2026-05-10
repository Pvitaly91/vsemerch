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
    <?  $form = ActiveForm::begin(['action' => ['mail/index'], 'id' => 'contact-form']); ?>
        <div class="order-product-information_block">
            <div class="order-block-information">
                <div class="order-information_input">
                    <div class="contact-infotmation_input">
                        <div class="contact-infotmation_input-text">
                             <p class="contact-infotmation_h1"><?= Yii::t('app', 'Mail send') ?></p>
                           <?= $form->field($model, 'name') ?>

                           <?= $form->field($model, 'email') ?>

                           <?= $form->field($model, 'subject') ?>

                           <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>

                           <?= $form->field($model, 'verifyCode')->widget(Captcha::className()) ?>

                           <div class="form-group">
                               <?= Html::submitButton(Yii::t('app', 'Submit send'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                           </div>


                        </div>
                    </div>
                </div>
            </div>
        </div> 
    <?php ActiveForm::end(); ?>
</div>

