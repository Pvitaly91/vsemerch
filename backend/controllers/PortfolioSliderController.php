<?php

namespace backend\controllers;

use backend\models\PortfolioSlider;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;


class PortfolioSliderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PortfolioSlider::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function handlePostSave(PortfolioSlider $model)
    {
        if ($model->load(\Yii::$app->request->post())) {
            $model->upload = UploadedFile::getInstance($model, 'upload');

            if ($model->validate()) {
                if ($model->upload) {
                    $fileName = uniqid() . '.' . $model->upload->extension;
                    $filePath = \Yii::getAlias('@frontend/web/upload/portfolio-slider/' . $fileName);
                    if ($model->upload->saveAs($filePath)) {
                        $model->image = $fileName;
                    }
                }

                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }
    }

    public function actionCreate()
    {
        $model = new PortfolioSlider();

        $this->handlePostSave($model);

        return $this->render('create', [
            'model' => $model,
        ]);

    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->handlePostSave($model);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {

        $model = $this->findModel($id);
        if($model->image) {
            $filePath = \Yii::getAlias('@frontend/web/upload/portfolio-slider/' . $model->image);
            @unlink($filePath);
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = PortfolioSlider::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
