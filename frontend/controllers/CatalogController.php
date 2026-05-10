<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Catalog;
use frontend\models\Text;
use yii\web\HttpException;

class CatalogController extends Controller
{
    public function actionIndex()
    {
        $slug = Yii::$app->request->get('slug');
        $text = null;

        if ($slug) {
            if(!$text = Text::find()->where(['slug'=>$slug])->one())
                throw new HttpException(404, 'Данной странице не существует!');
        }

        $model = Catalog::find()->all();

        return $this->render('index', [
            'model'=>$model,
            'text'=>$text,
        ]);
    }

    public function actionShow(){
        if(!$model = Catalog::find()->where(['translit'=>$_GET['translit']])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('show', [
            'model'=>$model,
        ]);
    }
}
