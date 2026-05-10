<?php
namespace frontend\controllers;

use Yii;
use frontend\models\Mail;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;


/**
 * Site controller
 */
class MailController extends Controller
{

    public function actionIndex()
    {
        $model = new Mail();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(defined(IS_LOCAL) && IS_LOCAL == true)
            {
                 Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            }else{
                 if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                    Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
                } else {
                    Yii::$app->session->setFlash('error', 'There was an error sending email.');
                }
                     
            }
           

            return Yii::$app->response->redirect(['/mail/success']);

        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }


    public function actionSuccess()
    {
        return $this->render('success', [

        ]);
    }

}