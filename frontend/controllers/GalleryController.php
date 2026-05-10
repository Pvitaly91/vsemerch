<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\FotosCat;
use frontend\models\Fotos;
use yii\web\HttpException;
use yii\data\Pagination;

class GalleryController extends Controller
{

    public function actionIndex2()
    {

        $query = FotosCat::find()->orderBy('id DESC') ;
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize'=>18]);
        $pages->forcePageParam = false;
        $pages->pageSizeParam = false;
        $news = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'pages'=>$pages,
            'news'=>$news,
        ]);
    }

    public function actionIndex(){
        $gallery = Fotos::find()->orderBy('id DESC')->all(); ;

        return $this->render('index', [
            'gallery'=>$gallery,
        ]);
    }

    public function actionShow(){
        if(!$gallery = FotosCat::find()->where(['id'=>$_GET['id']])->one())
            throw new HttpException(404, 'Данной странице не существует!');

        return $this->render('show', [
            'gallery'=>$gallery,
        ]);
    }

}