<?php
namespace backend\controllers;


use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use yii\data\ActiveDataProvider;
use \common\models\Widget;
use yii\web\HttpException;

class WidgetsController  extends Controller{
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
			'query' => Widget::find(),
			'pagination' => [
				'pageSize' => 20,
			],
		]);	
        return $this->render('index',['dataProvider'=>$dataProvider]);
    }
    public function actionSave()
    {
		$model = (!empty($_GET['id'])) ? Widget::findOne($_GET['id']) : new Widget;
       
        if($model == NULL || !isset($_GET['type'])){
            
            throw new HttpException(404, 'Данной странице не существует!');
        }
        
		if (Yii::$app->request->isPost ) 
        {
          
            $post = Yii::$app->request->post();
            $post["Widget"]["type"] = $_GET['type'];
        //    dd($post);
            $model->load($post);
            $model->save();
			return Yii::$app->response->redirect(['/widgets/index']);
		}
        return $this->render('save',['model'=>$model]);
    }

	public function actionDelete(){
		$model = Widget::findOne($_GET['id']);
		$model->delete();
		return Yii::$app->response->redirect(['/widgets/index']);
	}	
}    


