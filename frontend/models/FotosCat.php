<?php

namespace frontend\models;

use Yii;

class FotosCat extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fotos_cat';
    }

    public function getName()
    {
        $attribute = 'name_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getMeta_title()
    {
        $attribute = 'meta_title_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getMeta_keywords()
    {
        $attribute = 'meta_keywords_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getMeta_description()
    {
        $attribute = 'meta_description_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getBody()
    {
        $attribute = 'body_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getFotos()
    {
        return $this->hasMany(Fotos::className(), ['cat_id' => 'id'])->orderBy('id DESC');
    }

} 