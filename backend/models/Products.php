<?php

namespace backend\models;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Products extends \yii\db\ActiveRecord
{
    public $old_image;
    public $old_pdf;
    public $filename;
    public $catalog_parent;
    public $catalog;
    public $catalog_parent_image;
    public $catalog_image;
    public static function tableName()
    {
        return 'products';
    }
	
	public function rules()
	{
		return [
			[['name_ru'], 'required'],
			[['catalog_id','old_image','old_pdf','filename','body_ru','body_uk','name_uk','translit',
                            'about_ru','about_uk',
                            'meta_title_ru','meta_keywords_ru','meta_description_ru',
                            'meta_title_uk','meta_keywords_uk','meta_description_uk',
                            'sort'], 'safe'],
                        [['image'], 'file', 'extensions'=>'jpg, gif, png', 'skipOnEmpty'=>true],
                         [['pdf'], 'file', 'extensions'=>'pdf', 'skipOnEmpty'=>true],

        ]; 
	}	
	
	public function attributeLabels()
	{
		return [
			'name_ru'=>'Название',
            'name_uk'=>'Название uk',
            'name_en'=>'Название en',
			'body_ru'=>'Описание',
            'body_uk'=>'Описание uk',
            'body_en'=>'Описание en',
            'about_ru'=>'Краткое описание',
            'about_uk'=>'Краткое описание uk',
            'about_en'=>'Краткое описание en',
			'sort'=>'Сорт.',
                        'image'=>'Изображения',

		];
	}
        
	public function beforeSave($insert) {
		
      
                if (!$this->translit)
			$this->translit = Translite::rusencode($this->name_ru);
                
                
		if($image = UploadedFile::getInstance($this,'image')){			
			
                        $this->deleteImage($this->old_image);
                        //$this->image = $image;
            $this->image = ((!empty($this->filename)) ? $this->filename : time() . '_' . rand(1, 1000));
            $this->image .=  '.' . $image->extension;
                        $image->saveAs(Yii::getAlias('@frontend/web/upload/products/'.$this->image));
			
			$resizeObj = new resize(Yii::getAlias('@frontend/web/upload/products/'.$this->image));
			$resizeObj -> resizeImage(255, 170, 'crop');
                        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/products/ico/'.$this->image), 100);
			$resizeObj -> resizeImage(500, 500, 'crop');
                        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/products/big/'.$this->image), 100);
                }else $this->image = $this->old_image;

        if($pdf = UploadedFile::getInstance($this,'pdf')){

            $this->deletePdf($this->old_pdf);
            //$this->image = $image;
            $this->pdf =  time() . '_' . rand(1, 1000);
            $this->pdf .=  '.' . $pdf->extension;
            $pdf->saveAs(Yii::getAlias('@frontend/web/upload/products/pdf/'.$this->pdf));
        }else $this->pdf = $this->old_pdf;

		return parent::beforeSave($insert);
	}
        
        public function afterSave($insert, $changedAttributes) {
      
            
            return parent::afterSave($insert, $changedAttributes);
            
        }
        
        public function beforeDelete() {
            $this->deleteImage($this->image);
            $this->deletePdf($this->pdf);
            return parent::beforeDelete();
        }

    public function deletePdf($file){
        if(!empty($file)){
            @unlink(Yii::getAlias('@frontend/web/upload/products/pdf/'.$file));
        }
    }

    public function deleteImage($file){
                        if(!empty($file)){
                            @unlink(Yii::getAlias('@frontend/web/upload/products/'.$file));
                            @unlink(Yii::getAlias('@frontend/web/upload/products/ico/'.$file));
                            @unlink(Yii::getAlias('@frontend/web/upload/products/big/'.$file));
                        }            
        }          
        
        
        
      
	

}
