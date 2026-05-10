<?php

use yii\db\Migration;

/**
 * Class m200703_113836_shop_product_add_column_not_available
 */
class m270703_113836_shop_product_size_add_column_not_available extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_product_size}}', 'not_available', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%shop_product_size}}', 'not_available');
    }

}
