<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>

    <?php $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],

    ]); ?>

    <?= $form->field($model, 'text_id')->hiddenInput(['value'=>$_GET['textID']])->label(false); ?>


    <?= $form->field($model, 'sort') ?>

    <?= $form->field($model, 'name_ru') ?>

    <?= $form->field($model, 'name_uk') ?>

    <?= $form->field($model, 'name_en') ?>


    <?= $form->field($model, 'file')->fileInput() ?>


    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

