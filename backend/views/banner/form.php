<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>
<style>
    input[type=text]{
        max-width:150px;
    }
</style>
<?
$this->title = 'банера';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить банер</h1>


<?php $form = ActiveForm::begin([
    'id' => 'form',
    'options' => ['enctype' => 'multipart/form-data'],

]); ?>
<?= $form->field($model, 'sort') ?>
<?= $form->field($model, 'image')->fileInput() ?>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
<?php ActiveForm::end(); ?>