<?php

namespace backend\models;

use Yii;

class PortfolioSlider extends \yii\db\ActiveRecord
{
    public $upload;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'portfolio_slider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort'], 'required'],
            [['sort'], 'integer'],
            [['body_ru','body_uk'], 'string'],
            [['title_ru','title_uk'], 'string', 'max' => 255],
            [['upload'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sort' => 'Сортировка',
            'image' => 'Изображение',
            'body' => 'Описание',
        ];
    }

}
