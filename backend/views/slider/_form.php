<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ckeditor\CKEditor;
use app\mihaildev\elfinder\ElFinder;
?>

    <?php $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],

    ]); ?>

<?= $form->field($model, 'sort') ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'title_uk') ?>

    <?= $form->field($model, 'title_en') ?>


<?= $form->field($model, 'h1_ru') ?>

<?= $form->field($model, 'h1_uk') ?>

<?= $form->field($model, 'h1_en') ?>


<?= $form->field($model, 'h3_ru') ?>

<?= $form->field($model, 'h3_uk') ?>

<?= $form->field($model, 'h3_en') ?>


<?= $form->field($model, 'h2_ru') ?>

<?= $form->field($model, 'h2_uk') ?>

<?= $form->field($model, 'h2_en') ?>


    <?= $form->field($model, 'url_ru') ?>

    <?= $form->field($model, 'url_uk') ?>

    <?= $form->field($model, 'url_en') ?>

<?= $form->field($model, 'more_ru') ?>

<?= $form->field($model, 'more_uk') ?>

<?= $form->field($model, 'more_en') ?>

<?= $form->field($model, 'body_ru')->textarea() ?>

<?= $form->field($model, 'body_uk')->textarea() ?>

<?= $form->field($model, 'body_en')->textarea() ?>

    <?= $form->field($model, 'image')->fileInput() ?>
        
    <?= $form->field($model, 'old_image')->hiddenInput(['value'=>$model->image])->label(false); ?>    
       


    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

