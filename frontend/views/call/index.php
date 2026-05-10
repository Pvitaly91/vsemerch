<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>



            <?php $form = ActiveForm::begin(['action'=>['call/index'],'id' => 'call-form']); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'tel') ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Submit send'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>


