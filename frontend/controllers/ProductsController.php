<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\data\Pagination;
use frontend\models\Catalog;
use frontend\models\Products;


class ProductsController extends Controller
{

    public function actionIndex()
    {


                $model = Catalog::find()->orderBy('sort')->all() ;
            


            return $this->render('index', [
                'model'=>$model,
            ]);
    }
    
    public function actionSearch(){
                $query = Products::find()->innerJoinWith(['catalog']) ;
                if (!empty($_GET['search_str'])) {
                    $query->andWhere(['like', 'products.name_'.Yii::$app->language, $_GET['search_str']]);
                    $query->orWhere(['like', 'catalog.name_'.Yii::$app->language, $_GET['search_str']]);
                }
                $query->groupBy(['id']);
                $countQuery = clone $query;
                $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize'=>20]);
                $pages->forcePageParam = false;
                $pages->pageSizeParam = false;
                $products = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();

            return $this->render('search', [
                'pages'=>$pages,
                'products'=>$products,
            ]);        
    }
    
    public function actionShow(){

        if(!$product = Products::find()->where(['id'=>$_GET['id']])->one())
                    throw new HttpException(404, 'Данной странице не существует!');        
           

        return $this->render('show', [
                'product'=>$product,
            ]);
    }
    
    
  
    
}