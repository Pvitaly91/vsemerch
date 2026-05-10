<?php

namespace backend\models;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class DiseaseType extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'disease-type';
    }
	
	public function rules()
	{
		return [
			[['disease_id','title_ru'], 'required'],
			[['body_ru','body_uk',
                'title_uk','product_id'], 'safe'],

        ];
	}	
	
	public function attributeLabels()
	{
		return [
			'title_ru'=>'Название',
            'title_uk'=>'Название uk',
            'title_en'=>'Название en',
			'body_ru'=>'Описание',
            'body_uk'=>'Описание uk',
            'body_en'=>'Описание en',
            'about_ru'=>'Краткое описание',
            'about_uk'=>'Краткое описание uk',
            'about_en'=>'Краткое описание en',
			'sort'=>'Сорт.',
                        'image'=>'Изображения',
            'product_id'=>'Продукт',

		];
	}
        
	public function beforeSave($insert) {
		
		return parent::beforeSave($insert);
	}
        
        public function afterSave($insert, $changedAttributes) {
      
            
            return parent::afterSave($insert, $changedAttributes);
            
        }
        
        public function beforeDelete() {

            return parent::beforeDelete();
        }

        
        
        
      
	

}
