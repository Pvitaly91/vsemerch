<?php

use yii\db\Migration;

/**
 * Class m230729_193030_add_field_prtner_option
 */
class m230729_193030_add_field_prtner_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_product_option', 'partner', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230729_193030_add_field_prtner_option cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230729_193030_add_field_prtner_option cannot be reverted.\n";

        return false;
    }
    */
}
