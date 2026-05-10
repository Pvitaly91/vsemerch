<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;
use backend\models\TextFotos;


class TextFotosController extends Controller
{
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

    public function actionIndex($textID)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TextFotos::find()->where('text_id=:text_id', [':text_id' => $textID])->orderBy('id'),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionSave($id = null)
    {
        $model = (!empty($id)) ? TextFotos::findOne($id) : new TextFotos;

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {
                if (!empty($model->file)) {
                    $model->saveImage();
                }
                $model->save(false);
                return Yii::$app->response->redirect(['/text-fotos/index', 'textID' => $model->text_id]);
            }
        }
        return $this->render('save', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = TextFotos::findOne($id);
        $model->delete();
        return Yii::$app->response->redirect(['/text-fotos/index', 'textID' => $model->text_id]);
    }
}