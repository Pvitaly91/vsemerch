<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "option".
 *
 * @property integer $id
 * @property string $var
 * @property string $value
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['var', 'value_ru','value_uk'], 'required'],
            [['value_ru','value_uk'], 'string'],
            [['var'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'var' => 'Var',
            'value' => 'Value',
        ];
    }
}
