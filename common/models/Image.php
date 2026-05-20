<?php

namespace common\models;

use common\components\MultilingualQuery;
use navatech\language\Translate;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $product_id
 *
 * @property Product $product
 */
class Image extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_image';
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Language::getLanguagesAsArray(),
                'defaultLanguage' => 'ru',
                'langForeignKey' => 'shop_product_image_id',
                'tableName' => ImageTranslate::tableName(),
                'attributes' => [
                    'alt',
                ]
            ],
            [
                'class' => '\yiidreamteam\upload\ImageUploadBehavior',
                'attribute' => 'image',
                'thumbs' => [
                    'ico' => ['width' => 100, 'height' => 100],
                    'thumb' => ['width' => 400, 'height' => 400],
                ],
                'filePath' => '@frontend/web/upload/shop/products/image/[[pk]].[[extension]]',
                'fileUrl' => '/upload/shop/products/image/[[pk]].[[extension]]',
                'thumbPath' => '@frontend/web/upload/shop/products/image/thumb/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '/upload/shop/products/image/thumb/[[profile]]_[[pk]].[[extension]]',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['product_id'], 'integer'],
            [['product_id'], 'required'],
            ['image', 'image', 'skipOnEmpty' => true, 'extensions' => 'jpg, jpeg, gif, png'],
            [['alt'], 'required'],
            [['alt', 'alt_uk'], 'string', 'max' => 255],
        ];

        if ($this->hasAttribute('remote_image_url')) {
            $rules[] = [['remote_image_url'], 'string'];
        }

        return $rules;
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'image' => Yii::t('shop', 'image')
        ];
        return $attributeLabels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getLazyPic($profile = 'thumb')
    {
        $profile = in_array($profile, ['original', 'thumb', 'ico'], true) ? $profile : 'thumb';

        return '/img/product-image/' . (int)$this->id . '/' . $profile;
    }

}
