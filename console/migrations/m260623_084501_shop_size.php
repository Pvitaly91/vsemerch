<?php

use yii\db\Schema;
use yii\db\Migration;

class m260623_084501_shop_size extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shop_size}}', [
            'id' => Schema::TYPE_PK,
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%shop_product_size}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER,
            'size_id' => Schema::TYPE_INTEGER,
            'price' => $this->decimal(10, 2),
            'code' => $this->string()->null(),
        ], $tableOptions);

        $this->addForeignKey('fk-product_size_product_id-shop_product_id', '{{%shop_product_size}}', 'product_id', '{{%shop_product}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-product_size_size_id-shop_size_id', '{{%shop_product_size}}', 'size_id', '{{%shop_size}}', 'id', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_product_size}}');
        $this->dropTable('{{%shop_size}}');
    }
}
