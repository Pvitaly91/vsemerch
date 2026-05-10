<?php

namespace console\models;

use yii\db\ActiveRecord;


class Product extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_product';
    }
}
