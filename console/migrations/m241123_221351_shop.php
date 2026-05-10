<?php

use yii\db\Schema;
use yii\db\Migration;

class m241123_221351_shop extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shop_category}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER,
            'slug' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_category-parent_id-shop_category-id', '{{%shop_category}}', 'parent_id', '{{%shop_category}}', 'id', 'CASCADE');
        $this->createIndex('ix-shop_category-slug', '{{%shop_category}}', ['slug']);
        $this->createIndex('ix-shop_category-id-slug', '{{%shop_category}}', ['id', 'slug'],true);

        $this->createTable('{{%shop_category_translate}}', [
            'id' => Schema::TYPE_PK,
            'shop_category_id' => $this->integer()->notNull(),
            'language' => $this->string(6)->notNull(),
            'title' => Schema::TYPE_STRING,
            'body' => Schema::TYPE_TEXT,
            'meta_title' => Schema::TYPE_STRING,
            'meta_description' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_translate-category_id-shop_category_id', '{{%shop_category_translate}}', 'shop_category_id', '{{%shop_category}}', 'id', 'CASCADE');

        $this->createTable('{{%shop_product}}', [
            'id' => Schema::TYPE_PK,
            'image' => Schema::TYPE_STRING,
            'slug' => Schema::TYPE_STRING,
            'category_id' => Schema::TYPE_INTEGER,
            'code' => Schema::TYPE_STRING,
            'price' => $this->decimal(10, 2),
            'price_old' => $this->decimal(10, 2),
            'novelty' => $this->integer(1)->defaultValue(0),
            'action' => $this->integer(1)->defaultValue(0),
            'not_available' => $this->integer(1)->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product-category_id-shop_category_id', '{{%shop_product}}', 'category_id', '{{%shop_category}}', 'id', 'RESTRICT');
        $this->createIndex('ix-shop_product-slug', '{{%shop_product}}', ['slug']);
        $this->createIndex('ix-shop_product-id-slug', '{{%shop_product}}', ['id', 'slug'],true);
        $this->createIndex('ix-shop_product-novelty', '{{%shop_product}}', ['novelty']);
        $this->createIndex('ix-shop_product-action', '{{%shop_product}}', ['action']);
        $this->createIndex('ix-shop_product-not_available', '{{%shop_product}}', ['not_available']);

        $this->createTable('{{%shop_product_translate}}', [
            'id' => Schema::TYPE_PK,
            'shop_product_id' => $this->integer()->notNull(),
            'language' => $this->string(6)->notNull(),
            'title' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
            'meta_title' => Schema::TYPE_STRING,
            'meta_description' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_translate-product_id-shop_product_id', '{{%shop_product_translate}}', 'shop_product_id', '{{%shop_product}}', 'id', 'CASCADE');

        $this->createTable('{{%shop_product_image}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER,
            'image' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_image-product_id-shop_product_id', '{{%shop_product_image}}', 'product_id', 'shop_product', 'id', 'SET NULL');

        $this->createTable('{{%shop_product_image_translate}}', [
            'id' => Schema::TYPE_PK,
            'shop_product_image_id' => $this->integer()->notNull(),
            'language' => $this->string(6)->notNull(),
            'alt' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_image_translate-product_image_id-spi_id', '{{%shop_product_image_translate}}', 'shop_product_image_id', '{{%shop_product_image}}', 'id', 'CASCADE');

        $this->createTable('{{%shop_order}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING,
            'phone' => Schema::TYPE_STRING,
            'address' => Schema::TYPE_TEXT,
            'email' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_TEXT,
            'status' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->createTable('{{%shop_order_item}}', [
            'id' => Schema::TYPE_PK,
            'order_id' => Schema::TYPE_INTEGER,
            'code' => Schema::TYPE_STRING,
            'title' => Schema::TYPE_STRING,
            'price' => $this->decimal(10, 2),
            'product_id' => Schema::TYPE_INTEGER,
            'quantity' => Schema::TYPE_FLOAT,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_order_item-order_id-shop_order-id', '{{%shop_order_item}}', 'order_id', '{{%shop_order}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_order_item-product_id-shop_product-id', '{{%shop_order_item}}', 'product_id', '{{%shop_product}}', 'id', 'SET NULL');

        $this->createTable('{{%shop_product_option}}', [
            'id' => Schema::TYPE_PK,
            'product_id' => Schema::TYPE_INTEGER,
            'slug_option' => Schema::TYPE_STRING,
            'slug' => Schema::TYPE_STRING,
            'is_filter' => $this->integer(1)->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_option-product_id-shop_product-id', '{{%shop_product_option}}', 'product_id', '{{%shop_product}}', 'id', 'CASCADE');
        $this->createIndex('ix-shop_product_option-slug', '{{%shop_product_option}}', ['slug']);
        $this->createIndex('ix-shop_product_option-slug_option', '{{%shop_product_option}}', ['slug_option']);
        $this->createIndex('ix-shop_product_option-slug_option-slug', '{{%shop_product_option}}', ['slug_option', 'slug']);
        $this->createIndex('ix-shop_product_option-is_filter', '{{%shop_product_option}}', ['is_filter']);

        $this->createTable('{{%shop_product_option_translate}}', [
            'id' => Schema::TYPE_PK,
            'shop_product_option_id' => $this->integer()->notNull(),
            'language' => $this->string(6)->notNull(),
            'option' => Schema::TYPE_STRING,
            'value' => Schema::TYPE_STRING,
        ], $tableOptions);

        $this->addForeignKey('fk-shop_product_option_translate-product_option_id-spo_id', '{{%shop_product_option_translate}}', 'shop_product_option_id', '{{%shop_product_option}}', 'id', 'CASCADE');


    }

    public function down()
    {
        $this->dropTable('{{%shop_order_item}}');
        $this->dropTable('{{%shop_order}}');
        $this->dropTable('{{%shop_product_image_translate}}');
        $this->dropTable('{{%shop_product_image}}');
        $this->dropTable('{{%shop_product_option_translate}}');
        $this->dropTable('{{%shop_product_option}}');
        $this->dropTable('{{%shop_product_translate}}');
        $this->dropTable('{{%shop_product}}');
        $this->dropTable('{{%shop_category_translate}}');
        $this->dropTable('{{%shop_category}}');
    }
}
