<?php

use yii\db\Migration;

/**
 * Class m230616_212850_widgets
 */
class m230616_212850_widgets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('widgets', [
            'id' => $this->primaryKey(),
            'image_src' => $this->string(),
            'link_href' => $this->string(),
            'link_text_uk' => $this->string(),
            'link_text_ru' => $this->string(),
            'link_text_en' => $this->string(),
            'active' => $this->integer(1)->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_212850_widgets cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_212850_widgets cannot be reverted.\n";

        return false;
    }
    */
}
