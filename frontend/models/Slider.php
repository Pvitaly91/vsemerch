<?php

namespace frontend\models;

use Yii;

class Slider extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'slider';
    }
    
    public function getTitle()
    {
        $attribute = 'title_' . Yii::$app->language;
        return $this->{$attribute};
    } 
    
    public function getBody()
    {
        $attribute = 'body_' . Yii::$app->language;
        return $this->{$attribute};
    } 
    
    public function getUrl()
    {
        $attribute = 'url_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getMore()
    {
        $attribute = 'more_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getH1()
    {
        $attribute = 'h1_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getH2()
    {
        $attribute = 'h2_' . Yii::$app->language;
        return $this->{$attribute};
    }

        public function getH3()
    {
        $attribute = 'h3_' . Yii::$app->language;
        return $this->{$attribute};
    }

}    