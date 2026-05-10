<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categories_map}}`.
 */
class m230719_105031_create_categories_map_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('categories_map', [
            'id' => $this->primaryKey(),
            'partner_slug' => $this->string()->notNull(),
            'site_slug' => $this->string()->notNull(),
            'partner' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'type' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('categories_map');
    }
}
