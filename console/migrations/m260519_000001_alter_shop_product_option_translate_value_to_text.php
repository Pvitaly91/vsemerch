<?php

use yii\db\Migration;

class m260519_000001_alter_shop_product_option_translate_value_to_text extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%shop_product_option_translate}}', 'value', $this->text()->null());
    }

    public function safeDown()
    {
        $this->update(
            '{{%shop_product_option_translate}}',
            ['value' => new \yii\db\Expression('LEFT([[value]], 255)')],
            'CHAR_LENGTH([[value]]) > 255'
        );
        $this->alterColumn('{{%shop_product_option_translate}}', 'value', $this->string(255)->null());
    }
}
