<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use backend\models\News;


class BlogController extends Controller
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
			'query' => News::find()->where(["=","type","blog"]),
			'pagination' => [
				'pageSize' => 20,
			],
		]);	
        return $this->render('index',['dataProvider'=>$dataProvider]);
    }
	
    public function actionSave()
    {
		$model = (!empty($_GET['id'])) ? News::findOne($_GET['id']) : new News;

		if (($post = Yii::$app->request->post()) == true) {
            $post["News"]["type"] = "blog";


            $model->load($post);
            if($model->save())
			    return Yii::$app->response->redirect(['/blog/index']);
		}
        return $this->render('save',['model'=>$model]);
    }
    public function actionTest(){

    }
	public function actionDelete(){
		$model = News::findOne($_GET['id']);
		$model->delete();
		return Yii::$app->response->redirect(['/blog/index']);
	}	
}
