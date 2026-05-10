<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;


/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $categories common\models\Category[] */
/* @var $form yii\widgets\ActiveForm */
$treeSelect = new \alex290\treeselect\TreeSelect();
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sort'); ?>

    <?= $form->field($model, 'upload')->fileInput() ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'body_ru')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'title_uk') ?>

    <?= $form->field($model, 'body_uk')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'title_en') ?>

    <?= $form->field($model, 'body_en')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
