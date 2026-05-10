<?php

use yii\db\Migration;

/**
 * Class m191219_113025_add_column_alt_table_catalog
 */
class m191219_113025_add_column_alt_table_catalog extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('catalog', 'alt_ru', $this->string()->null());
        $this->addColumn('catalog', 'alt_uk', $this->string()->null());
        $this->addColumn('catalog', 'alt_en', $this->string()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191219_113025_add_column_alt_table_catalog cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191219_113025_add_column_alt_table_catalog cannot be reverted.\n";

        return false;
    }
    */
}
