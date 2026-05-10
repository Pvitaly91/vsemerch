<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\Disease;


class DiseaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout','index'],
                'rules' => [
                    [
                        'actions' => ['index','save','delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Disease::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index',['dataProvider'=>$dataProvider]);
    }

    public function actionSave()
    {
        $model = (!empty($_GET['id'])) ? Disease::findOne($_GET['id']) : new Disease;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return Yii::$app->response->redirect(['/disease/index']);
        }
        return $this->render('save',['model'=>$model]);
    }

    public function actionDelete(){
        $model = Disease::findOne($_GET['id']);
        $model->delete();
        return Yii::$app->response->redirect(['/disease/index']);
    }
}
