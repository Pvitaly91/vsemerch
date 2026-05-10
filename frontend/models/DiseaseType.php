<?php

namespace frontend\models;


use Yii;

class DiseaseType extends \yii\db\ActiveRecord
{

    
    public static function tableName()
    {
        return 'disease-type';
    }
    

        
        public function getProduct()
        {
            return $this->hasOne(Products::className(), ['id' => 'product_id']);
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


} 