<?php

namespace backend\models;

use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Slider extends \yii\db\ActiveRecord
{
	public $old_image;
        
	public static function tableName()
    {
        return 'slider';
    }
	
	public function rules()
	{
		return [
			[['title_ru'], 'required'],
			[['old_image','sort',
				'url_ru','url_uk',
				'h1_ru','h1_uk',
				'h2_ru','h2_uk',
				'h3_ru','h3_uk',
				'more_ru','more_uk',
				'body_ru','body_uk',
				'title_uk',
			], 'safe'],
                        [['image'], 'file', 'extensions'=>'jpg, gif, png', 'skipOnEmpty'=>true],

                    ];
	}	
	
	public function attributeLabels()
	{
		return [
			'title_ru'=>'Название',
			'body_ru'=>'Описание',
			'title_en'=>'Название en',
			'body_en'=>'Описание en',                    
			'sort'=>'Сорт.',
                        'image'=>'Изображения',
		];
	}
        
	public function beforeSave($insert) {
                
                
		if($image = UploadedFile::getInstance($this,'image')){			

                        $this->deleteImage($this->old_image);
                        //$this->image = $image;
			$this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;
                        $image->saveAs(Yii::getAlias('@frontend/web/upload/slider/'.$this->image));
			
		}else $this->image = $this->old_image;                

		return parent::beforeSave($insert);
	}
        
        public function beforeDelete() {
            $this->deleteImage($this->image); 
            return parent::beforeDelete();
        }
        
        public function deleteImage($file){ 
                        if(!empty($file)){
                            @unlink('upload/slider/'.$file);
                        }            
        }        
	

}

