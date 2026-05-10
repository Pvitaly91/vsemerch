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

<?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::className(), ['clientOptions' => [], 'options' => ['class' => 'form-control', 'style' => 'width:150px;'], 'dateFormat' => 'yyyy-MM-dd',]) ?>

<?= $form->field($model, 'title_ru') ?>

<?= $form->field($model, 'title_uk') ?>

<?= $form->field($model, 'title_en') ?>




<? /*$form->field($model, 'translit', [
            'addon' => ['prepend' => ['content'=>'@']]
        ]);*/ ?>
<?= $form->field($model, 'slug', [
    'inputTemplate' => '<div class="input-group" style="z-index:1;"><span class="input-group-addon">articles/</span>{input}</div>',
]); ?>

<?= $form->field($model, 'image')->fileInput() ?>

<?= $form->field($model, 'old_image')->hiddenInput(['value' => $model->image])->label(false); ?>

<?= $form->field($model, 'body_ru')->widget(CKEditor::className(), [
    'editorOptions' => ElFinder::ckeditorOptions('elfinder', [/* Some CKEditor Options */]),
]);
?>

<?= $form->field($model, 'body_uk')->widget(CKEditor::className(), [
    'editorOptions' => ElFinder::ckeditorOptions('elfinder', [/* Some CKEditor Options */]),
]);
?>

<?= $form->field($model, 'body_en')->widget(CKEditor::className(), [
    'editorOptions' => ElFinder::ckeditorOptions('elfinder', [/* Some CKEditor Options */]),
]);
?>

<?= $form->field($model, 'meta_title_ru') ?>
<?= $form->field($model, 'meta_title_en') ?>
<?= $form->field($model, 'meta_title_uk') ?>



<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>


<?php ActiveForm::end(); ?>

