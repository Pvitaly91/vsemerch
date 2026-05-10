<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Text;
use yii\web\HttpException;

class TextController extends Controller
{

    public function actionIndex()
    {

            if(!$modelText = Text::find()->where(['slug'=>$_GET['slug']])->one())
                    throw new HttpException(404, 'Данной странице не существует!');
            
            return $this->render('index', [
                    'text'=>$modelText,
            ]);
    }

    public function actionAbout()
    {

        if(!$modelText = Text::find()->where(['slug'=>'about'])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('index', [
            'text'=>$modelText,
        ]);
    }

    public function actionContact()
    {

        if(!$modelText = Text::find()->where(['slug'=>'contact'])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('index', [
            'text'=>$modelText,
        ]);
    }

    public function actionTeam()
    {

        if(!$modelText = Text::find()->where(['translit'=>'team'])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('index', [
            'text'=>$modelText,
        ]);
    }
    
}