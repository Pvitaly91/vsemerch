<?php

use yii\db\Migration;

/**
 * Class m200702_134559_shop_add_column_partner_name
 */
class m270702_134559_shop_add_column_partner_slug extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_category', 'partner_slug', $this->string()->null());
        $this->execute("update shop_category set partner_slug=slug where partner!=''");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shop_category', 'partner_slug');
    }
}
