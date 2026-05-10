<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\WebCatalog;
use frontend\models\WebProducts;
use yii\web\HttpException;

class WebController extends Controller
{

   /* public function actionIndex()
    {
            
            $model = WebProducts::find()->all();

            
            return $this->render('index', [
                    'model'=>$model,
            ]);
    }

    public function actionShow(){

        if(!$model = WebCatalog::find()->where(['translit'=>$_GET['translit']])->one())
            throw new HttpException(404, 'Данной странице не существует!');


        return $this->render('show', [
            'model'=>$model,
        ]);
    }*/

    
}