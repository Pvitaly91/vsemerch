<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\data\Pagination;
use frontend\models\WebCatalog;
use frontend\models\WebProducts;


class WebProductsController extends Controller
{

    public function actionIndex()
    {


                $model = WebCatalog::find()->orderBy('sort')->all() ;
            


            return $this->render('index', [
                'model'=>$model,
            ]);
    }
    

    
    public function actionShow(){

        if(!$model = WebProducts::find()->where(['id'=>$_GET['id']])->one())
                    throw new HttpException(404, 'Данной странице не существует!');        
           

        return $this->render('show', [
                'model'=>$model,
            ]);
    }
    
    
  
    
}