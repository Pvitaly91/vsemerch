<?php
namespace common\models;


use yii\db\ActiveRecord;



class ProductMap extends ActiveRecord
{
    public static function tableName()
    {
        return 'products_map';
    }

}
