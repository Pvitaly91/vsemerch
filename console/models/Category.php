<?php

namespace console\models;

use yii\db\ActiveRecord;


class Category extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_category';
    }

}
