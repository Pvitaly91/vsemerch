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

    <?= $form->field($model, 'link') ?>

    <?= $form->field($model, 'translit',[
        'inputTemplate' => '<div class="input-group"><span class="input-group-addon">products/</span>{input}</div>',
    ]);?>
        
    <?= $form->field($model, 'image')->fileInput() ?>


    <?= $form->field($model, 'old_image')->hiddenInput(['value'=>$model->image])->label(false); ?>

    <?= $form->field($model, 'pdf')->fileInput() ?>


    <?= $form->field($model, 'old_pdf')->hiddenInput(['value'=>$model->pdf])->label(false); ?>

    <?= $form->field($model, 'about_ru')->textarea() ?>
    <?= $form->field($model, 'about_uk')->textarea() ?>
    <?= $form->field($model, 'about_en')->textarea() ?>

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



<?= $form->field($model, 'meta_title_ru') ?>

<?= $form->field($model, 'meta_title_uk') ?>

<?= $form->field($model, 'meta_title_en') ?>

<?= $form->field($model, 'meta_keywords_ru') ?>

<?= $form->field($model, 'meta_keywords_uk') ?>

<?= $form->field($model, 'meta_keywords_en') ?>

<?= $form->field($model, 'meta_description_ru') ?>

<?= $form->field($model, 'meta_description_uk') ?>

<?= $form->field($model, 'meta_description_en') ?>


    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

