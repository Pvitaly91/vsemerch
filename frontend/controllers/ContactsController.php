<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Text;
use yii\web\HttpException;

class ContactsController extends Controller
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    public function actionIndex()
    {


        $modelText = Text::find()->where(['slug'=>'contacts'])->one();

        return $this->render('index', [
            'text'=>$modelText,
        ]);
    }


    public function actionSuccess(){
        $modelText = Text::find()->where(['slug'=>'contacts'])->one();
        return $this->render('success', [
            'text'=>$modelText,
        ]);
    }

}