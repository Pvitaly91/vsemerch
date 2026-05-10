<?php

namespace backend\models;

use Yii;
use common\components\Translite;

/**
 * This is the model class for table "faq".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 */
class Faq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['body'], 'string'],
            [['title', 'slug', 'meta_title', 'meta_description', 'meta_keywords'], 'string', 'max' => 250],

            [[ 'slug', 'body', 'meta_title', 'meta_description', 'meta_keywords'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'body' => 'Body',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
        ];
    }

    public function beforeSave($insert) {
        if (!$this->slug)
            $this->slug = Translite::rusencode($this->title);

        return parent::beforeSave($insert);
    }
}
