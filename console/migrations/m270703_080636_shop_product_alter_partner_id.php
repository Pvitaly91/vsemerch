<?php

use yii\db\Migration;

/**
 * Class m200703_080636_shop_product_alter_partner_id
 */
class m270703_080636_shop_product_alter_partner_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('shop_product', 'partner_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
