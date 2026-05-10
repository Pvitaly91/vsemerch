<?php
namespace common\models;

use yii\db\ActiveRecord;

Class ProductTranslate extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_product_translate';
    }
}