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

    <?= $form->field($model, 'sort') ?>

    <?= $form->field($model, 'name_ru') ?>

    <?= $form->field($model, 'name_uk') ?>

    <?= $form->field($model, 'name_en') ?>

    <? /*$form->field($model, 'translit', [
            'addon' => ['prepend' => ['content'=>'@']]
        ]);*/?>
    <?= $form->field($model, 'translit',[
        'inputTemplate' => '<div class="input-group"><span class="input-group-addon">web/</span>{input}</div>',
    ]);?>
        
    <?= $form->field($model, 'image')->fileInput() ?>
        
    <?= $form->field($model, 'old_image')->hiddenInput(['value'=>$model->image])->label(false); ?>    

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

