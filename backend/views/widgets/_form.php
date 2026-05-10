<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>

<?php $form = ActiveForm::begin([
    'id' => 'form',
    'options' => ['enctype' => 'multipart/form-data'],

]); ?>

<?= $form->field($model, 'sort') ?>
<? if($model->isNewRecord):?>
    <?= $form->field($model, "active")->checkbox(['checked' => true]); ?>
<? else:?>
    <?= $form->field($model, "active")->checkbox(); ?>
<? endif;?>
<?= $form->field($model, 'link_text_uk') ?>

<?= $form->field($model, 'link_text_ru') ?>

<?= $form->field($model, 'link_text_en') ?>

<?= $form->field($model, 'link_href'); ?>

<?= $form->field($model, 'image_src')->fileInput() ?>

<?//= $form->field($model, 'old_image')->hiddenInput(['value' => $model->image_src])->label(false); ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>


<?php ActiveForm::end(); ?>

