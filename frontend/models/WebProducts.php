<?php

namespace frontend\models;


use Yii;

class WebProducts extends \yii\db\ActiveRecord
{

    
    public static function tableName()
    {
        return 'web_products';
    }
    
	public function rules()
	{
		return [
			[['fasovka','type','brends'], 'safe'],
                    ];
	}    
    
	public function attributeLabels()
	{
		return [
			'fasovka'=>'Фасовка',
                        'type'=>'Типы',
                        'brends'=>'Бренды',
		];
	}
        
        public function getCatalog()
        {
            return $this->hasOne(WebCatalog::className(), ['id' => 'catalog_id']);
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
    

} 