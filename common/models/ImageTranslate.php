<?php
namespace common\models;

use yii\db\ActiveRecord;

Class ImageTranslate extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_product_image_translate';
    }
}