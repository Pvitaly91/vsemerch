<?php

namespace backend\models;

use Yii;
use common\components\Translite;
use common\components\resize;
use yii\web\UploadedFile;

class Articles extends \yii\db\ActiveRecord
{
    public $old_image;

    public static function tableName()
    {
        return 'articles';
    }

    public function rules()
    {
        return [
            [['title_ru', 'date'], 'required'],
            [['old_image', 'body_ru', 'text_ru', 'text_uk',  'slug', 'meta_title_ru', 'meta_keywords_ru', 'meta_description_ru',
               
                'title_uk', 'body_uk', 'meta_title_uk', 'meta_keywords_uk', 'meta_description_uk',
                'alt_ru', 'alt_uk'
                ], 'safe'],
            [['image'], 'file', 'extensions' => 'jpg, gif, png', 'skipOnEmpty' => true],

        ];
    }

    public function attributeLabels()
    {
        return [
            'title_ru' => 'Название',
            'text_ru' => 'под заголовок',
            'text_uk' => 'под заголовок Uk',
            'text_en' => 'под заголовок En',
            'body_ru' => 'Описание',
            'title_en' => 'Название en',
            'body_en' => 'Описание en',
            'date' => 'Дата',
            'meta_description_en' => 'Краткое описание Англ',
            'meta_description_ru' => 'Краткое описание',
            'meta_description_uk' => 'Краткое описание Укр',
            'image' => 'Изображения',
            'top' => 'ТОП новость'
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->slug)
            $this->slug = Translite::rusencode($this->title_ru);


        if ($image = UploadedFile::getInstance($this, 'image')) {

            $this->deleteImage($this->old_image);
            //$this->image = $image;
            $this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;

            $image->saveAs(Yii::getAlias('@frontend/web/upload/articles/' . $this->image));

            $resizeObj = new resize(Yii::getAlias('@frontend/web/upload/articles/' . $this->image));
            $resizeObj->resizeImage(250, 180, 'crop');
            $resizeObj->saveImage(Yii::getAlias('@frontend/web/upload/articles/ico/' . $this->image), 100);
            $resizeObj->resizeImage(400, 400, 'crop');
            $resizeObj->saveImage(Yii::getAlias('@frontend/web/upload/articles/big/' . $this->image), 100);
        } else $this->image = $this->old_image;

        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        $this->deleteImage($this->image);
        return parent::beforeDelete();
    }

    public function deleteImage($file)
    {
        if (!empty($file)) {
            @unlink(Yii::getAlias('@frontend/web/upload/articles/' . $file));
            @unlink(Yii::getAlias('@frontend/web/upload/articles/ico/' . $file));
            @unlink(Yii::getAlias('@frontend/web/upload/articles/big/' . $file));
        }
    }


}

