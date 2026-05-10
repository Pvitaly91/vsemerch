<?php

namespace frontend\models;

use Yii;

class Articles extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'articles';
    }
    
    public function getTitle()
    {
        $attribute = 'title_' . Yii::$app->language;
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
      public function getText()
    {
        $attribute = 'text_' . Yii::$app->language;
        return $this->{$attribute};
    }    
    public function getBody()
    {
        $attribute = 'body_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getAlt()
    {
        $attribute = 'alt_' . Yii::$app->language;
        return $this->{$attribute};
    }
    
} 