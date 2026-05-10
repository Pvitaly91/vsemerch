<?php

namespace backend\models;
use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Disease extends \yii\db\ActiveRecord
{
    public $old_image;

    public static function tableName()
    {
        return 'disease';
    }

    public function rules()
    {
        return [
            [['name_ru'], 'required'],
            [['sort','old_image','body_ru','body_uk','translit',
                'meta_title_ru','meta_title_uk',
                'meta_keywords_ru','meta_keywords_uk',
                'meta_description_ru',
                'name_uk',
                'meta_description_uk'], 'safe'],
            [['image'], 'file', 'extensions'=>'jpg, gif, png', 'skipOnEmpty'=>true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name_ru'=>'Название',
            'body_ru'=>'Описание',
            'name_en'=>'Название en',
            'body_en'=>'Описание en',
            'sort'=>'Сорт.',
            'parent_id'=>'Родитель',
            'image'=>'Изображения',
        ];
    }

    public function beforeSave($insert) {

        if (!$this->translit)
            $this->translit = Translite::rusencode($this->name_ru);


        if($image = UploadedFile::getInstance($this,'image')){

            $this->deleteImage($this->old_image);
            //$this->image = $image;
            $this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;
            $image->saveAs(Yii::getAlias('@frontend/web/upload/disease/'.$this->image));

            $resizeObj = new resize(Yii::getAlias('@frontend/web/upload/disease/'.$this->image));
            $resizeObj -> resizeImage(500, 500, 'crop');
            $resizeObj -> saveImage(Yii::getAlias('@frontend/web/upload/disease/big/'.$this->image), 100);
        }else $this->image = $this->old_image;

        return parent::beforeSave($insert);
    }

    public function beforeDelete() {
        $this->deleteImage($this->image);
        return parent::beforeDelete();
    }

    public function deleteImage($file){
        if(!empty($file)){
            @unlink(Yii::getAlias('@frontend/web/upload/disease/'.$file));
            @unlink(Yii::getAlias('@frontend/web/upload/disease/big/'.$file));
        }
    }




}

