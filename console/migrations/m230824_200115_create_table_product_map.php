<?php

use yii\db\Migration;

/**
 * Class m230824_200115_create_table_product_map
 */
class m230824_200115_create_table_product_map extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->createTable('products_map', [
            'id' => $this->primaryKey(),
            'partner_product_id' => $this->string()->notNull(),
            'product_partner' => $this->string()->notNull(),
            'category_partner_id' => $this->string()->notNull(),
            'category_partner' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'type' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230824_200115_create_table_product_map cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230824_200115_create_table_product_map cannot be reverted.\n";

        return false;
    }
    */
}
