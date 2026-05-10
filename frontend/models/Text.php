<?php
namespace frontend\models;

use Yii;

class Text extends \yii\db\ActiveRecord
{
    use \common\models\traits\EditLink;
    public static function tableName()
    {
        return 'text';
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
    public function getBody()
    {
        $attribute = 'body_' . Yii::$app->language;
        return $this->{$attribute};
    }

    public function getFotos()
    {
        return $this->hasMany(TextFotos::className(), ['text_id' => 'id'])->orderBy('sort ASC');
    }

}    