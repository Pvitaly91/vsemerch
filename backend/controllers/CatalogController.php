<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\Catalog;


class CatalogController extends Controller
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
	$model = new Catalog;
        
        $dataCatalog = new ArrayDataProvider([
            'allModels' => $model->getDataTree(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ]
            ]);
                
        return $this->render('index',['dataCatalog'=>$dataCatalog]);
    }
	
    public function actionSave()
    {
       
        $model = (!empty($_GET['id'])) ? Catalog::findOne($_GET['id']) : new Catalog;
      
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

                return Yii::$app->response->redirect(['/catalog/index']);
        }
        return $this->render('save',['model'=>$model]);
    }

	public function actionDelete(){
		$model = Catalog::findOne($_GET['id']);
		$model->delete();
		return Yii::$app->response->redirect(['/catalog/index']);
	}	
}
