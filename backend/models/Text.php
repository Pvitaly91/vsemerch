<?php

namespace backend\models;
use common\components\Translite;

class Text extends \yii\db\ActiveRecord
{
	
	public static function tableName()
    {
        return 'text';
    }
	
	public function rules()
	{
		return [
			[['title_ru'], 'required'],
			[['body_ru','body_uk','slug',
				'meta_title_ru','meta_title_uk',
				'meta_keywords_ru','meta_keywords_uk',
				'meta_description_ru',
				'title_uk',
				'meta_description_uk'], 'safe'],
		];
	}	
	
	public function attributeLabels()
	{
		return [
			'title'=>'Название',
			'body'=>'Описание',
			'sort'=>'Сорт.',
		];
	}
        
	public function beforeSave($insert) {
		if (!$this->slug)
			$this->slug = Translite::rusencode($this->title_ru);

		return parent::beforeSave($insert);
	}        
	

}

