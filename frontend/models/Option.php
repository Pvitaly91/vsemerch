<?php

namespace frontend\models;

use Yii;

class Option extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'option';
    }

    public function getValue()
    {
        $attribute = 'value_' . Yii::$app->language;
        return $this->{$attribute};
    }

    static public function show($var){
        if($model = self::find()->where('var=:var',[':var'=>$var])->one())
        return $model->value;
        else return;
    }


} 