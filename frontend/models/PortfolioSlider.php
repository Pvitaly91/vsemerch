<?php
namespace frontend\models;

use Yii;

class PortfolioSlider extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'portfolio_slider';
    }
    
    public function getTitle()
    {
        $attribute = 'title_' . Yii::$app->language;
        return $this->{$attribute};
    }
    public function getBody()
    {
        $attribute = 'body_' . Yii::$app->language;
        return $this->{$attribute};
    }

}    