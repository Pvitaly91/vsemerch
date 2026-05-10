<?php

namespace frontend\models;

use Yii;

class Partners extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'partners';
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

    public function getImageAvatar(){
        return !empty($this->image) ? $this->image : 'no_image.jpg';
    }
} 