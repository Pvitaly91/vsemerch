<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Text;
use yii\web\HttpException;

class ProductionController extends Controller
{

    public function actionIndex()
    {
            
            if(!$modelText = Text::find()->where(['slug'=>'production'])->one())
                    throw new HttpException(404, 'Данной странице не существует!');

            return $this->render('index', [
                    'text'=>$modelText,
            ]);
    }

    public function actionShow()
    {

        if(!$modelText = Text::find()->where(['slug'=>$_GET['slug']])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('show', [
            'text'=>$modelText,
        ]);
    }

    
}