<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%option_map}}`.
 */
class m230731_214448_create_option_map_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('option_map', [
            'id' => $this->primaryKey(),
            'partner_slug' => $this->string()->notNull(),
            'site_slug' => $this->string()->notNull(),
            'category_id' => $this->integer()->null(),
            'partner' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'status' => $this->string()->null()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('option_map');
    }
}
