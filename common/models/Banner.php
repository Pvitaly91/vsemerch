<?php 

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use common\components\resize;
use common\components\Translite;
use yii\behaviors\SluggableBehavior;

class Banner extends ActiveRecord
{
    public $old_image;

    public $bannerPath = "/upload/banner/";
    public static function tableName()
    {
        return 'banner';
    }
    public function rules()
    {
        return [
            [['old_image'], 'safe'],
            [['sort'],'integer'],
            [['image'], 'file', 'extensions' => 'jpg, gif, png', 'skipOnEmpty' => true],

        ];
    }

    public function beforeSave($insert)
    {
      
       $path = Yii::getAlias('@frontend/web'.$this->bannerPath);
        if(!is_dir( $path) && !mkdir( $path)){
            die('Не удалось создать директории...');
        }   
        $this->created_at = date('Y-m-d H:i:s');
        if ($image = UploadedFile::getInstance($this, 'image')) {

            $this->deleteImage($this->old_image);
            //$this->image = $image;
            $this->image = time() . '_' . rand(1, 1000) . '.' . $image->extension;

            $image->saveAs(Yii::getAlias('@frontend/web'.$this->bannerPath . $this->image));

            $resizeObj = new resize(Yii::getAlias('@frontend/web'.$this->bannerPath . $this->image));
         
            $resizeObj->resizeImage(260, 345, 'crop');
            $resizeObj->saveImage(Yii::getAlias('@frontend/web'.$this->bannerPath . $this->image), 100);
        } else $this->image = $this->old_image;

        return parent::beforeSave($insert);
    }

    public function beforeDelete() {
        $this->deleteImage($this->image);
        return parent::beforeDelete();
    }

    public function deleteImage($file)
    {
        if (!empty($file)) {
            @unlink(Yii::getAlias('@frontend/web'.$this->bannerPath . $file));

        }
    }
}