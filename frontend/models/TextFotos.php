<?php

namespace frontend\models;

use Yii;

class TextFotos extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'text_fotos';
    }

    public function getName()
    {
        $attribute = 'name_' . Yii::$app->language;
        return $this->{$attribute};
    }
} 