<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="product-form">
    <?php
    $form = ActiveForm::begin(['id' => 'product-form']);
    echo $form->errorSummary($model);
    ?>
    <div class="row">
        <div class="col-md-8">

            <?= $form->field($model, 'image')->fileInput() ?>

            <?php
            $items = [];
            foreach (\common\models\Language::getLanguages() as $key => $language):

                if($language->code != Yii::$app->language) {
                    $fields = [
                        'alt' => 'alt_' . $language->code,
                    ];
                } else {
                    $fields = [
                        'alt' => 'alt',
                    ];
                }

                $content = $form->field($model, $fields['alt']);

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
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('shop', 'Create') : Yii::t('shop', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
