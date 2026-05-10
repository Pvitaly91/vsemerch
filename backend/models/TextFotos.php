<?php
namespace backend\models;

use Yii;
use common\components\resize;
use yii\web\UploadedFile;

Class TextFotos extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return 'text_fotos';
    }

    public function rules()
    {
        return [
            [['name_ru', 'text_id'], 'required'],
            [['catalog_id','name_en', 'name_uk', 'sort'], 'safe'],
            [['file'], 'file', 'extensions'=>'jpg, gif, png'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name_ru'=>'Название',
            'name_uk'=>'Название uk',
            'name_en'=>'Название en',
            'sort'=>'Сорт.',
            'file'=>'Изображения',
        ];
    }

    public function saveImage()
    {
        $this->deleteImage();
        $filename = uniqid() . '.' . $this->file->extension;
        $this->file->saveAs(Yii::getAlias('@frontend/web/upload/text_fotos/'. $filename));
        $resizeObj = new resize(Yii::getAlias('@frontend/web/upload/text_fotos/'. $filename));
        $resizeObj -> resizeImage(255, 170, 'crop');
        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/text_fotos/ico/'. $filename), 100);
        $resizeObj -> resizeImage(500, 500, 'crop');
        $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/text_fotos/big/'. $filename), 100);
        $this->image = $filename;
    }

    public function beforeDelete() {
        $this->deleteImage();
        return parent::beforeDelete();
    }

    public function deleteImage(){
        if(!empty($this->image)){
            @unlink(Yii::getAlias('@frontend/web/upload/text_fotos/'.$this->image));
            @unlink(Yii::getAlias('@frontend/web/upload/text_fotos/ico/'.$this->image));
            @unlink(Yii::getAlias('@frontend/web/upload/text_fotos/big/'.$this->image));
        }
    }
}