<?php

namespace backend\models;

use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Partners extends \yii\db\ActiveRecord
{
	public $old_image;
        
	public static function tableName()
    {
        return 'partners';
    }
	
	public function rules()
	{
		return [
			[['title_ru'], 'required'],
			[['old_image','url','title_uk',
				'body_ru','body_uk'], 'safe'],
                        [['image'], 'file', 'extensions'=>'jpg, gif, png', 'skipOnEmpty'=>true],

                    ];
	}	
	
	public function attributeLabels()
	{
		return [
			'title_ru'=>'Название',
			'body_ru'=>'Описание',
			'body_uk'=>'Описание uk',
			'title_en'=>'Название en',
			'title_uk'=>'Название uk',
			'body_en'=>'Описание en',                    
			'url'=>'Url',
                        'image'=>'Изображения',
		];
	}
        
	public function beforeSave($insert) {

                
                
		if($image = UploadedFile::getInstance($this,'image')){			

                        $this->deleteImage($this->old_image);
                        //$this->image = $image;
			$this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;
                        $image->saveAs(Yii::getAlias('@frontend/web/upload/partners/'.$this->image));
			
			//$resizeObj = new resize(Yii::getAlias('@frontend/web/upload/partners/'.$this->image));
			//$resizeObj -> resizeImage(300, 140, 'auto');
                //        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/partners/ico/'.$this->image), 100);
                        
		}else $this->image = $this->old_image;                

		return parent::beforeSave($insert);
	}
        
        public function beforeDelete() {
            $this->deleteImage($this->image); 
            return parent::beforeDelete();
        }
        
        public function deleteImage($file){ 
                        if(!empty($file)){
                            @unlink(Yii::getAlias('@frontend/web/upload/partners/'.$file));
                            //@unlink('upload/partners/ico/'.$file);
                        }            
        }        
	

}

