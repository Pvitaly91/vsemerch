<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\data\Pagination;
use frontend\models\Disease;


class DiseaseController extends Controller
{

    public function actionIndex()
    {


       // $model = Disease::find()->orderBy('sort')->all() ;



        return $this->render('index', [
            //'model'=>$model,
        ]);
    }



    public function actionShow(){

        if(!$disease = Disease::find()->where(['translit'=>$_GET['translit']])->one())
            throw new HttpException(404, 'Данной странице не существует!');


        return $this->render('show', [
            'disease'=>$disease,
        ]);
    }




}