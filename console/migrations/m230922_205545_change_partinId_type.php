<?php

use yii\db\Migration;

/**
 * Class m230922_205545_change_partinId_type
 */
class m230922_205545_change_partinId_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('shop_category', 'partner_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('shop_category', 'partner_id', $this->integer()->null());

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230922_205545_change_partinId_type cannot be reverted.\n";

        return false;
    }
    */
}
