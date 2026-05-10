<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\components\ckeditor\CKEditor;
use app\mihaildev\elfinder\ElFinder;
use app\modules\admin\models\Catalog;
?>
<?
$this->title = 'Мульти загрузка';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1>Мульти загрузка</h1>

<?php $form = ActiveForm::begin([
    'id' => 'form',
    'options' => ['enctype' => 'multipart/form-data'],

]); ?>

<?= $form->field($model, 'cat_id')->hiddenInput(['value'=>$_GET['catID']])->label(false); ?>




<?= $form->field($model, 'image[]')->fileInput(['multiple' => true]) ?>





<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>



<?php ActiveForm::end(); ?>