<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\Fotos;


class FotosController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['logout','index'],
                'rules' => [
                    [
                        'actions' => ['index','save','upload','delete','delete-all'],
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
			'query' => Fotos::find()->where('cat_id=:cat_id',[':cat_id'=>$_GET['catID']])->orderBy('id'),
			'pagination' => [
				'pageSize' => 20,
			],
		]);	
        return $this->render('index',['dataProvider'=>$dataProvider]);
    }
	
    public function actionSave()
    {
		$model = (!empty($_GET['id'])) ? Fotos::findOne($_GET['id']) : new Fotos;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
			return Yii::$app->response->redirect(['/fotos/index','catID'=>$_GET['catID']]);
		}
        return $this->render('save',['model'=>$model]);
    }


    public function actionUpload(){

        $model =  new Fotos;
        if ($model->load(Yii::$app->request->post()) && $model->upload()) {

            return Yii::$app->response->redirect(['/fotos/index','catID'=>$_GET['catID']]);
        }
        return $this->render('upload',['model'=>$model]);
    }

	public function actionDelete($id){
		$model = Fotos::findOne($id);
		$model->delete();
		return Yii::$app->response->redirect(['/fotos/index','catID'=>$_GET['catID']]);
	}

    public function actionDeleteAll(){
        if(!empty($_POST['selection'])){
            foreach($_POST['selection'] as $selection){
                $model = Fotos::findOne($selection);
                $model->delete();
            }
        }
        return Yii::$app->response->redirect(Yii::$app->request->referrer);
    }
}

