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
    <?= $form->field($model, "min_quantity")->textInput(['type' => 'number']);?>
    
    <? if(isset($_GET["parent_id"]) ):?> 
        <? if(is_numeric($_GET["parent_id"])):?>
    
            <input type="hidden" name="Category[parent_id]" value="<?=$_GET["parent_id"]?>">
        <? endif;?>
    <? else:?>    
        <?=$form->field($model, 'parent_id')->dropDownList($treeSelect->getTree($categories), ['prompt' => 'Root (Главная)']) ?>
    <? endif;?>
            
      
    <? if($partner != "main"):?>        
        <?= $form->field($model, 'catalog_id')->dropDownList($pageList, ['prompt' => '--//--']) ?>
	
        <?= $form->field($model, "link")->textInput(['type' => 'text']);?>
    <? endif;?>        
    <?= $form->field($model, 'copy_category')->dropDownList($treeSelect->getTree($categories), ['prompt' => 'Не копировать']) ?>

    <?= $form->field($model, "sort")->textInput(['type' => 'number']);?>

    <?= $form->field($model, "active")->checkbox(['checked'=>true]); ?>
    <?php
    $items = [];
    foreach (\common\models\Language::getLanguages() as $key => $language):

        if($language->code != Yii::$app->language) {
            $fields = [
                'title' => 'title_' . $language->code,
                'body' => 'body_' . $language->code,
                'seo' => 'seo_' . $language->code,
                'meta_title' => 'meta_title_' . $language->code,
                'meta_description' => 'meta_description_' . $language->code,
            ];
        } else {
            $fields = [
                'title' => 'title',
                'body' => 'body',
                'seo' => 'seo',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description'
            ];
        }

        $content = $form->field($model, $fields['title']);
        $content .= $form->field($model, $fields['body'])->widget(CKEditor::className(),[
            'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
        ]);
        $content .= $form->field($model, $fields['seo'])->widget(CKEditor::className(),[
            'editorOptions' => ElFinder::ckeditorOptions('elfinder',[/* Some CKEditor Options */]),
        ]);
        $content .= $form->field($model, $fields['meta_title']);
        $content .= $form->field($model, $fields['meta_description']);

        $items[] = [
            'label' => $language->code,
            'content' => $content,
            'active' => ($language->code === Yii::$app->language)
        ];
    endforeach;

    echo \yii\bootstrap\Tabs::widget([
        'items' => $items
    ]);
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
