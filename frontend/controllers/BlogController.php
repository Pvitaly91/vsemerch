<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\News;
use yii\web\HttpException;
use yii\data\Pagination;

class BlogController extends Controller
{

    public function actionIndex()
    {
//ALTER TABLE `news` ADD `type` VARCHAR(12) NULL DEFAULT NULL AFTER `alt_en`;

                $query = News::find()->where(["=","type","blog"])->orderBy('id DESC') ;
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
    
    public function actionShow(){
        if(!$news = News::find()->where(['id'=>$_GET['id']])->andWhere(["=","type","blog"])->one())
                    throw new HttpException(404, 'Данной странице не существует!');        
    
            return $this->render('show', [
                'news'=>$news,
            ]);
    }
    
}