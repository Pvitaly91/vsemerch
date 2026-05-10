<?php

namespace frontend\models;


use Yii;

class Disease extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'disease';
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

    public function getAbout()
    {
        $attribute = 'about_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getImageAvatar(){
        return !empty($this->image) ? $this->image : 'no_image.jpg';
    }

    public function getType()
    {
        return $this->hasMany(DiseaseType::className(), ['disease_id' => 'id'])->orderBy('id ASC');
    }
} 