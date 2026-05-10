<?php

namespace console\models;

use yii\db\ActiveRecord;

class ProductOption extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_option';
    }

	public function save($runValidation = true, $attributeNames = null){
        $this->orginal_slug = $this->slug;
        return parent::save($runValidation,$attributeNames);
    }
}
