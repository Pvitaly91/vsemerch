<?php
namespace common\models;

use yii\db\ActiveRecord;

Class ProductOptionTranslate extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_product_option_translate';
    }
}