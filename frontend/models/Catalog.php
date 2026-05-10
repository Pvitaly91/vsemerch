<?php

namespace frontend\models;

use Yii;

class Catalog extends \yii\db\ActiveRecord
{
    use \common\models\traits\EditLink;
    public static function tableName()
    {
        return 'catalog';
    }
    
    static public function getCatalogs($parent_id = 0){
       return self::find()->where(['parent_id'=>$parent_id])->orderBy('sort ASC')->with('childs')->all(); 
    }
    
    public function getChilds()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])->orderBy('sort ASC');
    }
    
    public function getProducts()
    {
        return $this->hasMany(Products::className(), ['catalog_id' => 'id'])->orderBy('sort ASC');
    }    
    
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }
    
    public function getName()
    {
        $attribute = 'name_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getAlt()
    {
        $attribute = 'alt_' . Yii::$app->language;
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

    public function getImageAvatar(){
        return !empty($this->image) ? $this->image : 'no_image.jpg';
    }
    
    
}    