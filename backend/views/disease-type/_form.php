<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use backend\models\Products;

?>

    <?php $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],

    ]); ?>

    <?= $form->field($model, 'disease_id')->hiddenInput(['value'=>$_GET['catID']])->label(false); ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'title_uk') ?>

    <?= $form->field($model, 'title_en') ?>

    <?=$form->field($model, 'body_ru')->widget(CKEditor::className(),[
    'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
    ]);
    ?>

    <?=$form->field($model, 'body_uk')->widget(CKEditor::className(),[
        'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
    ]);
    ?>

    <?=$form->field($model, 'body_en')->widget(CKEditor::className(),[
    'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
    ]);
    ?>


    <?= $form->field($model, 'product_id')
        ->dropDownList(ArrayHelper::map(Products::find()->asArray()->orderBy('name_ru')->all(), 'id', 'name_ru'), ['prompt' => 'Выберите продукт...'])
    ?>


    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

