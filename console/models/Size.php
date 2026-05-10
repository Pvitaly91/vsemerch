<?php

namespace console\models;

use Yii;

class Size extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_size';
    }
}
