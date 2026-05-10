<?php

namespace backend\models;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Fotos extends \yii\db\ActiveRecord
{
    public $old_image;
    public $_image;
    
    public static function tableName()
    {
        return 'fotos';
    }
	
	public function rules()
	{
		return [
			[['cat_id'], 'safe'],
			[['name','old_image'], 'safe'],
                        [['image'], 'file', 'extensions'=>'jpg,jpeg, gif, png', 'maxFiles' => 1000, 'skipOnEmpty'=>true],
                    ];
	}	
	
	public function attributeLabels()
	{
		return [
			'name'=>'Название',
			'cost'=>'Цена',
			'sort'=>'Сорт.',
                        'image'=>'Изображения',
		];
	}
        
	public function beforeSave($insert) {
        $image = (!empty($this->_image)) ? $this->_image : UploadedFile::getInstance($this,'image');
        if($image){

                        $this->deleteImage($this->old_image);
                        //$this->image = $image;
			$this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;
                        $image->saveAs(Yii::getAlias('@frontend/web/upload/fotos/'.$this->image));

			$resizeObj = new resize(Yii::getAlias('@frontend/web/upload/fotos/'.$this->image));
			$resizeObj -> resizeImage(200, 200, 'crop');
                        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/fotos/ico/'.$this->image), 100);
                    }else $this->image = $this->old_image;
                
		return parent::beforeSave($insert);
	}

    public function upload(){
        $images = UploadedFile::getInstances($this, 'image');
       // print_r($images);exit;
        foreach ($images as $image) {
            $model = new self;
            $model->cat_id = $this->cat_id;
            $model->_image = $image;
            $model->save();
        }
        return true;
    }
        
        public function beforeDelete() {
            $this->deleteImage($this->image); 
            return parent::beforeDelete();
        }
        
        public function deleteImage($file){ 
                        if(!empty($file)){
                            @unlink(Yii::getAlias('@frontend/web/upload/fotos/'.$file));
                            @unlink(Yii::getAlias('@frontend/web/upload/fotos/ico/'.$file));
                            @unlink(Yii::getAlias('@frontend/web/upload/fotos/big/'.$file));
                        }            
        }
        
        
        
       
      
	

}
