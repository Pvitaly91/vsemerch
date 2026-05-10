<?php

use yii\db\Migration;

/**
 * Class m230920_200415_add_more_photo_column_to_products
 */
class m230920_200415_add_more_photo_column_to_products extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_product', 'more_photos', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('shop_product', 'more_photos');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230920_200415_add_more_photo_column_to_products cannot be reverted.\n";

        return false;
    }
    */
}
