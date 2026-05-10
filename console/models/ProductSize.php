<?php

namespace console\models;

use Yii;
use yii\db\ActiveRecord;

class ProductSize extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_product_size';
    }
}
