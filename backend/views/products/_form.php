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

    <?= $form->field($model, 'catalog_id')->hiddenInput(['value'=>$_GET['catID']])->label(false); ?>


    <?= $form->field($model, 'sort') ?>

    <?= $form->field($model, 'name_ru') ?>

    <?= $form->field($model, 'name_uk') ?>

    <?= $form->field($model, 'name_en') ?>


    <?= $form->field($model, 'image')->fileInput() ?>


    <?= $form->field($model, 'old_image')->hiddenInput(['value'=>$model->image])->label(false); ?>





    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

