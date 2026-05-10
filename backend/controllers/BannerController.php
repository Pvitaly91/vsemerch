<?php 

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Banner;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class BannerController extends Controller{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout','index'],
                'rules' => [
                    [
                        'actions' => ['index','create','delete'],
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

    public function actionCreate(){
        $model = new Banner();
        
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
			return Yii::$app->response->redirect(['/banner/index']);
		}
       
        return $this->render('form',['model'=>$model]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
			'query' => Banner::find(),
			'pagination' => [
				'pageSize' => 20,
			],
		]);	
        return $this->render('index',['dataProvider'=>$dataProvider]);
        
    }

    public function actionDelete(){
		$model = Banner::findOne($_GET['id']);
		$model->delete();
		return Yii::$app->response->redirect(['/banner/index']);
	}	
}