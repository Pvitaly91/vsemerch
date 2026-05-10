<?php

namespace frontend\models;

use Yii;


class Menu extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'menu';
    }


    public function getName()
    {
        $attribute = 'name_' . Yii::$app->language;
        return $this->{$attribute};
    }    

    public static function itemsMenu(){
        $items = [];
        foreach(self::find()->orderBy('sort')->all() as $item){
            $items[] = ['label' => $item->name, 'url' => [$item->url]];
        }
        return $items;
    }
}    