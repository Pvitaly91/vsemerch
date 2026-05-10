<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
?>
<?php
$form = ActiveForm::begin([
            'id' => 'form',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>
<div class="item panel panel-default"><!-- widgetBody -->
    <div class="panel-heading">
        <h3 class="panel-title pull-left">Характеристика <?= strtoupper($lng) ?></h3>
        <div class="pull-right">
            <button type="button" class="remove-item btn btn-danger btn-xs"><i
                    class="glyphicon glyphicon-minus"></i></button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <div class="form-group field-product-action">

           
            <label>
                <input type="checkbox" id="product-action" name="ProductOptionTranslate[flag]" value="1">Переименовать только для категории етого товара
            </label>

            <div class="help-block"></div>
        </div>
        <?= $form->field($modelOption, 'option') ?>
        </div>
    </div>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



<?php ActiveForm::end(); ?>