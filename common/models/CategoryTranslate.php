<?php
namespace common\models;

use yii\db\ActiveRecord;

Class CategoryTranslate extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_category_translate';
    }
}