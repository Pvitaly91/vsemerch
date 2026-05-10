<?php

namespace common\models;


use yii\db\ActiveRecord;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Widget extends ActiveRecord
{
     use \common\models\traits\EditLink;
     public $old_image;
     private $imgPath = "/upload/widgets/";
     public static $types = [
         1 => "ПОЛІГРАФІЧНА ПРОДУКЦІЯ",
         2 => "СУВЕНІРНА ПРОДУКЦІЯ"
     ];
     function getLngPrefix(){
        $defaultLng = "uk";
        $lngPrefix = "";
        if(\Yii::$app->language != $defaultLng){
            $lngPrefix ="/".\Yii::$app->language;
        }
        return $lngPrefix;
     }
     public static function tableName()
    {
        return 'widgets';
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image_src' => 'Фото',
            'link_href' => 'Ссылка на страницу',
            'link_text_uk' => 'Название UA',
            'link_text_ru' => 'Название RU',
            'link_text_en' => 'Название EN',
            "active" => "Активно",
            "sort" => "Сортировка"
        ];
    }
    public function getLink()
    {
        $link = $this->getLngPrefix().$this->link_href ;
        return $link;
    }
    public function getLink_text()
    {
        
        $attribute = 'link_text_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getImage()
    {
        if(strpos($this->image_src,"/") === false){
            $this->image_src = $this->imgPath.$this->image_src;
        }
     //   if()
       
        return $this->image_src;
    }
     public function getIco()
    {
        if(strpos($this->image_src,"/") === false){
            $this->image_src = $this->imgPath."ico/".$this->image_src;
        }
     //   if()
       
        return $this->image_src;
    }
     public function rules()
    {
        return [
          
            [['old_image','link_href', 'link_text_uk', 'link_text_ru', 'active', 'sort', 'type' ], 'safe'],
            [['image_src'], 'file', 'extensions' => 'jpg, gif, png', 'skipOnEmpty' => true],

        ];
    }
     public function beforeSave($insert)
    {
               

        if ($image = UploadedFile::getInstance($this, 'image_src')) {
        
            $this->deleteImage($this->old_image);
     
            $this->image_src = time() . '_' . rand(1, 1000) . '.' . $image->extension;

            $image->saveAs(Yii::getAlias('@frontend/web'.$this->imgPath . $this->image_src));
            
            $resizeObj = new resize(Yii::getAlias('@frontend/web'.$this->imgPath . $this->image_src));
            $resizeObj->resizeImage(176, 186, 'crop');
            $resizeObj->saveImage(Yii::getAlias('@frontend/web'.$this->imgPath."ico/" . $this->image_src), 100);
            
            
        } elseif($this->old_image != ''){
         
             $this->image_src = $this->old_image;
        }else{
           $this->image_src=  $this->oldAttributes['image_src'];
        }

        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        $this->deleteImage($this->image_src);
        return parent::beforeDelete();
    }

    public function deleteImage($file)
    {
        if (!empty($file)) {
            @unlink(Yii::getAlias('@frontend/web'.$this->imgPath . $file));
            @unlink(Yii::getAlias('@frontend/web'.$this->imgPath."ico/" . $file));
        }
    }
}    