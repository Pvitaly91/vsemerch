<?php

namespace backend\models;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class FotosCat extends \yii\db\ActiveRecord
{
    public $old_image;

    public static function tableName()
    {
        return 'fotos_cat';
    }

    public function rules()
    {
        return [
            [['name_ru'], 'required'],
            [['old_image'], 'safe'],
           // [['image'], 'file', 'extensions'=>'jpg, gif, png', 'skipOnEmpty'=>true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name_ru'=>'Название',
            'body_ru'=>'Описание',
            'body_en'=>'Описание EN',
            'sort'=>'Сорт.',
            'image'=>'Изображения',
        ];
    }

    public function beforeSave($insert) {
        if($image = UploadedFile::getInstance($this,'image')){

            $this->deleteImage($this->old_image);
            //$this->image = $image;
            $this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;
            $image->saveAs(Yii::getAlias('@frontend/web/upload/fotos_cat/'.$this->image));

            $resizeObj = new resize(Yii::getAlias('@frontend/web/upload/fotos_cat/'.$this->image));
            $resizeObj -> resizeImage(176, 159, 'crop');
            $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/fotos_cat/ico/'.$this->image), 100);
            $resizeObj -> resizeImage(704, 372, 'crop');
            $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/fotos_cat/big/'.$this->image), 100);
        }else $this->image = $this->old_image;

        return parent::beforeSave($insert);
    }

    public function beforeDelete() {
        $this->deleteImage($this->image);
        return parent::beforeDelete();
    }

    public function deleteImage($file){
        if(!empty($file)){
            @unlink(Yii::getAlias('@frontend/web/upload/fotos_cat/'.$file));
            @unlink(Yii::getAlias('@frontend/web/upload/fotos_cat/ico/'.$file));
            @unlink(Yii::getAlias('@frontend/web/upload/fotos_cat/big/'.$file));
        }
    }







}
