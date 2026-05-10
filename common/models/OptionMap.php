<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "option_map".
 *
 * @property int $id
 * @property string $partner_slug
 * @property string $site_slug
 * @property string $partner
 * @property string $created_at
 * @property string|null $status
 */
class OptionMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'option_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_slug', 'site_slug', 'partner', 'created_at'], 'required'],
            [['created_at'], 'safe'],
            [['partner_slug', 'site_slug', 'partner', 'status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_slug' => 'Partner Slug',
            'site_slug' => 'Site Slug',
            'partner' => 'Partner',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}
