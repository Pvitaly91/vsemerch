<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\components\ckeditor\CKEditor;
use app\mihaildev\elfinder\ElFinder;
use app\modules\admin\models\Catalog;
?>

    <?php $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],

    ]); ?>

    <?= $form->field($model, 'cat_id')->hiddenInput(['value'=>$_GET['catID']])->label(false); ?>

    <?= $form->field($model, 'name') ?>

       
      <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'old_image')->hiddenInput(['value'=>$model->image])->label(false); ?>



    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



    <?php ActiveForm::end(); ?>

