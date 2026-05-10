<?php

namespace frontend\models;

use Yii;

class WebCatalog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'web_catalog';
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
        return $this->hasMany(WebProducts::className(), ['catalog_id' => 'id'])->orderBy('sort ASC');
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