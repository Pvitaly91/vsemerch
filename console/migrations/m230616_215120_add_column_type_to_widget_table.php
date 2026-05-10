<?php

use yii\db\Migration;

/**
 * Class m230616_215120_add_column_type_to_widget_table
 */
class m230616_215120_add_column_type_to_widget_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('widgets', 'type', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_215120_add_column_type_to_widget_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_215120_add_column_type_to_widget_table cannot be reverted.\n";

        return false;
    }
    */
}
