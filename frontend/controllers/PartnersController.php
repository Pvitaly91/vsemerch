<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use frontend\models\Partners;


class PartnersController extends Controller
{

	
    public function actionIndex()
    {
		$model = Partners::find()->orderBy('id')->all();
        return $this->render('index',['model'=>$model]);
    }
	

}
