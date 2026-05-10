<?php

namespace frontend\controllers;

use frontend\models\PortfolioSlider;
use Yii;
use yii\web\Controller;
use frontend\models\Text;
use yii\web\HttpException;

class PortfolioController extends Controller
{


    public function actionIndex()
    {

        $modelText = Text::find()->where(['slug'=>'portfolio'])->one();

        $model = PortfolioSlider::find()->orderBy('sort')->all();

        return $this->render('index', [
            'text'=>$modelText,
            'model'=>$model,
        ]);
    }


}