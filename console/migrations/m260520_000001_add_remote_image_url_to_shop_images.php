<?php

use yii\db\Migration;

class m260520_000001_add_remote_image_url_to_shop_images extends Migration
{
    public function safeUp()
    {
        if (!$this->columnExists('{{%shop_product}}', 'remote_image_url')) {
            $this->addColumn('{{%shop_product}}', 'remote_image_url', $this->text()->null());
        }

        if (!$this->columnExists('{{%shop_product_image}}', 'remote_image_url')) {
            $this->addColumn('{{%shop_product_image}}', 'remote_image_url', $this->text()->null());
        }
    }

    public function safeDown()
    {
        if ($this->columnExists('{{%shop_product_image}}', 'remote_image_url')) {
            $this->dropColumn('{{%shop_product_image}}', 'remote_image_url');
        }

        if ($this->columnExists('{{%shop_product}}', 'remote_image_url')) {
            $this->dropColumn('{{%shop_product}}', 'remote_image_url');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        $schema = $this->db->getTableSchema($table, true);

        return $schema !== null && isset($schema->columns[$column]);
    }
}
