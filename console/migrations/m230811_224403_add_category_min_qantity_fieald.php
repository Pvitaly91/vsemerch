<?php

use yii\db\Migration;

/**
 * Class m230811_224403_add_category_min_qantity_fieald
 */
class m230811_224403_add_category_min_qantity_fieald extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_category', 'min_quantity', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230811_224403_add_category_min_qantity_fieald cannot be reverted.\n";

        $this->dropColumn('shop_category', 'min_quantity');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230811_224403_add_category_min_qantity_fieald cannot be reverted.\n";

        return false;
    }
    */
}
