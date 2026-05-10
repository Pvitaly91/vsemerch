<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\ProductOption;
use common\models\ProductOptionTranslate;
use yii\web\HttpException;
use common\models\Product;
use common\models\Category;
use common\models\ProductMap;
class ProductMapController  extends Controller {
    function actionCreate(){
        if(\Yii::$app->request->isPost && ($post = \Yii::$app->request->post()) == true){
            
        
            $uuidParts = explode("_",$post["uuid"]);
            
            $category_partner = $uuidParts["0"];
            $category_partner_id = $uuidParts["1"];
            $ids = $titles =[];
            $refer = $post["refer"];
        //    var_dump($category_partner);
         //   print_r($category_partner_id);
        //    dd($category_partner,$category_partner_id,$category_partner);
            foreach($post['products'] as $id){
               
                $prod = Product::find()->where(["id" =>$id])->one();
                 
                $titles[$id] = $prod->title;
                if($prod->sku_group ){
                   $prods = Product::find()->where(["sku_group" => $prod->sku_group])->all();
                 //  dd($prods);
                    foreach($prods as $prod){
                        $category = Category::find()->where(["partner_id" => $category_partner_id,"partner" => $category_partner])->one();
                        $prod->category_id = $category->id;
                        if($prod->save(false)){
                            if (!$producMapModel = ProductMap::find()->where(['partner_product_id' => $prod->partner_id, 'product_partner' => $prod->partner])->one()) {
                                $producMapModel = new ProductMap();
                            }
                          
                            $producMapModel->category_partner_id = $category_partner_id;
                            $producMapModel->category_partner = $category_partner;
                            $producMapModel->partner_product_id = $prod->partner_id;
                            $producMapModel->product_partner = $prod->partner;
                            $producMapModel->created_at = date('Y-m-d H:i:s');
                            if($producMapModel->save(false)){
                                $prod->morePhotos();
                            }
                            
              
                        }
                   
                      
                   }
                }
               
             
            }
         //s   Yii::$app->session->setFlash('success', "Готово");
             
            return Yii::$app->response->redirect($refer);
        }
    }
    function actionIndex(){
        //dd($_POST);
     
        if(\Yii::$app->request->isGet && ($post = \Yii::$app->request->get("productsId")) == true){
           $titles =[];
           $refer = $_GET["refer"];
        
            foreach($post as $id){
               $prod = Product::find()->where(["id" =>$id])->one();
                $titles[$id] = $prod->title;
            }    
            
            
             $models = Category::find()
            ->where(['in','partner' , \common\Helpers\Partners::$_main]) 
            ->andWhere(["active" => 1])  
            ->all();
        
        
        


            $cats = [];
            $catsBase = [];
            
            $nullParent = [];
            $mainCatsId = [];
            foreach($models as $k => $model){
                if($model['link'] != null){
                    continue;
                }
                if($model->parent_id == NULL){
                    $mainCatsId[$model->slug] = $model->id;
                    $cats[$model->id] = [
                        "slug" =>$model->slug,
                       // "name" => $model->title
                    ];
                    $nullParent[$model->slug] = $model->slug;
                    unset($models[$k]);
                }
                 $catsBase[$model->slug] = [
                     "id" => $model->id,
                     "title" => $model->title." ".strtoupper($model->partner)
                 ];
            }

            $menu = [];

            foreach($models as $k => $model){
                if(isset($cats[$model->parent_id])){
                    $menu[$cats[$model->parent_id]["slug"]][$model->slug] = $model->partner."_".$model->partner_id;//$model->slug."-".$model->id;
                    unset($nullParent[$cats[$model->parent_id]["slug"]]);
                }
            }
            foreach($nullParent as $cat){
                $menu[$cat] = [];
            }
          
            return $this->render('index', [
                'model' => $model,
                'menu' => $menu,
                'titles' => $titles,
                'catsBase' => $catsBase,
                'refer' => $refer,
                'mainCatsId' => $mainCatsId
        
               
            ]);
        }else{
            throw new HttpException(404, 'Данной странице не существует!');
        }
    }
}