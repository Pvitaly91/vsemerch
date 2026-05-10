<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use common\models\ShopSize;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
$treeSelect = new \alex290\treeselect\TreeSelect();

$url_option = Url::to(['autocomplete-option']);
$url_value = Url::to(['autocomplete-value']);
$script = <<< JS
$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    //console.log(item);
    $( item ).find('.option').autocomplete({"source":"{$url_option}"});
    $( item ).find('.value').autocomplete({"source":"{$url_value}"});
          
    ptabs($(item).find('.ptabs')); 
    
    $( item ).find('input:checkbox').prop('checked', true);
   
});
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="product-form">
    <a href="/admin/shop/product/update?id=<?=$model->id?>&delphotos=<?=$model->id?>"><strong>Удалить фото</strong></a>
    <br><br>
     
    <?php
    $form = ActiveForm::begin(['id' => 'product-form']);
    echo $form->errorSummary($model);
    ?>
    <div class="row">
        <div class="col-md-8">

            <?= $form->field($model, 'category_id')->dropDownList($treeSelect->getTree($categories), ['prompt' => 'Выберите категорию']) ?>
			
			<?= $form->field($model, 'transfer_all')->checkbox() ?>

            <?= $form->field($model, 'code')->textInput(['maxlength' => 19]) ?>

            <?= $form->field($model, 'image')->fileInput() ?>

            <?php

                $content = $form->field($model, 'price')->textInput(['maxlength' => 19]);
                $content .= $form->field($model, 'price_old')->textInput(['maxlength' => 19]);
                $items[] = [
                'label' => 'Цена',
                'content' => $content,
                'active' => true
                ];

                $content = '';
                foreach (\common\models\Size::find()->all() as $i => $size) {
                    $modelSize = $size->sizeProduct($model->id);
                    $content .= '<h3>' . $size->name . '</h3>';
                    $content .= Html::activeHiddenInput($modelSize, "[{$i}]id");
                    $content .= $form->field($modelSize, '['.$i.']size_id')->hiddenInput(['value' => $size->id])->label(false);
                    $content .= $form->field($modelSize, '['.$i.']price');
                    $content .= $form->field($modelSize, '['.$i.']code');
                }
//                $items[] = [
//                    'label' => 'Размеры',
//                    'content' => $content,
//                    'active' => false
//                ];

                echo \yii\bootstrap\Tabs::widget([
                'items' => $items
                ]);
            ?>

            <?= $form->field($model, 'action')->checkbox() ?>

            <?= $form->field($model, 'novelty')->checkbox() ?>

            <?= $form->field($model, 'not_available')->checkbox() ?>

            <?php
            $items = [];
            foreach (\common\models\Language::getLanguages() as $key => $language):

                if($language->code != Yii::$app->language) {
                    $fields = [
                        'title' => 'title_' . $language->code,
                        'description' => 'description_' . $language->code,
                        'meta_title' => 'meta_title_' . $language->code,
                        'meta_description' => 'meta_description_' . $language->code,
                    ];
                } else {
                    $fields = [
                        'title' => 'title',
                        'description' => 'description',
                        'meta_title' => 'meta_title',
                        'meta_description' => 'meta_description'
                    ];
                }

                $content = $form->field($model, $fields['title']);
                $content .= $form->field($model, $fields['description'])->widget(CKEditor::className(),[
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
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Характеристики / фильтры</div>
                <div class="panel-body">
                    <div id="filters_result"></div>

                    <?php
                    $formFields = [];
                    foreach (\common\models\Language::getLanguages() as $key => $language):
                        $formFields[] = [
                            "option_" . $language->code,
                            "value_" . $language->code
                        ];
                    endforeach;
                    $formFields[] = ['is_filter'];
                    $formFields = call_user_func_array('array_merge', $formFields);
                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 100, // the maximum times, an element can be cloned (default 999)
                        'min' => 0, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelsOption[0],
                        'formId' => 'product-form',
                        'formFields' => $formFields,
                    ]); ?>
                    <div class="container-items"><!-- widgetContainer -->
                        <?php foreach ($modelsOption as $i => $modelOption): ?>
                        <?
                        if($modelOption->slug_option == "naavnist")
                        {
                            continue;
                        }
                        ?>
                            <div class="item panel panel-default"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">Характеристика</h3>
                                    <div class="pull-right">
                                        <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                                    class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelOption->isNewRecord) {
                                        echo Html::activeHiddenInput($modelOption, "[{$i}]id");
                                        $checked = ($modelOption->is_filter) ? true : false;
                                    } else {
                                        $checked = true;
                                    }
                                    ?>
                                    <?=$form->field($modelOption, "[{$i}]is_filter")->checkbox(['checked' => $checked])?>
                                    <?php
                                    $items = [];
                                    foreach (\common\models\Language::getLanguages() as $key => $language):
                                        
                                        if($language->code != Yii::$app->language) {
                                            $fields = [
                                                'option' => "[{$i}]option_" . $language->code,
                                                'value' => "[{$i}]value_" . $language->code,
                                            ];
                                        } else {
                                            $fields = [
                                                'option' => "[{$i}]option",
                                                'value' => "[{$i}]value",
                                            ];
                                        }
                                        
                                        $content = $form->field($modelOption, $fields['option'])->widget(\yii\jui\AutoComplete::classname(), [
                                            'clientOptions' => [
                                                'source' => Url::to(['autocomplete-option']),
                                            ], 'options' => [
                                                'class' => 'form-control option',
                                            ],
                                        ]);
                                        $content .= $form->field($modelOption, $fields['value'])->widget(\yii\jui\AutoComplete::classname(), [
                                            'clientOptions' => [
                                                'source' => Url::to(['autocomplete-value']),
                                            ], 'options' => [
                                                'class' => 'form-control value',
                                            ],
                                        ]);

                                        $items[] = [
                                            'label' => $language->code,
                                            'content' => $content,
                                            'active' => ($language->code === Yii::$app->language),
                                        ];
                                    endforeach;

                                    echo \common\widgets\Tabs::widget([
                                        'items' => $items
                                    ]);
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-item btn btn-success"><i class="glyphicon glyphicon-plus"></i>
                        Добавить характеристику
                    </button>

                    <?php DynamicFormWidget::end(); ?>


                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
