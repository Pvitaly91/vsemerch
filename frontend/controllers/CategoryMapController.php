<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\web\HttpException;
use common\models\Category;
use common\models\CategoriesMap;
use Yii;
class CategoryMapController extends Controller
{
    function actionDelete(){
        if(!\common\Helpers\Partners::isAdmin() || !isset($_GET["id"])){
            throw new HttpException(404, 'Данной странице не существует!');
        }
        if(isset($_GET["pId"]) ){
           // $cat = Category::find()->where(["id" => $_GET["id"]])->one();
           // $catMap = CategoriesMap::find()->where(["site_slug" => $cat->slug])->one();
            
           // dd($catMap);
           if( 
                (
                $mainPartnerCat= Category::find()->where(["id" => $_GET["pId"]])->one()) == true   
                && ($catMap = CategoriesMap::find()->where(["partner_slug" => $mainPartnerCat->slug])->one()) == true
                && $catMap->delete()
                )
            {
                $mainPartnerCat->active = 1;
                $mainPartnerCat->update(true);
                Yii::$app->session->setFlash('success', "Склейка успешно удалено");
                return $this->redirect('/');
            }    
        }else{
            // if del subcat mapping
            if( 
                ($cat = Category::find()->where(["id" => $_GET["id"]])->one()) == true
                && ($catMap = CategoriesMap::find()->where(["partner_slug" => $cat->slug,"partner" => $cat->partner])->one()) == true
                && ($mainPartnerCatId = Category::find()->where(["partner" => $cat->partner, "slug" => $cat->partner])->one()->id) == true    
                && $catMap->delete())
            {
                $cat->parent_id = $mainPartnerCatId;
                $cat->update(true);
                Yii::$app->session->setFlash('success', "Склейка успешно удалено");
                return $this->redirect('/');
            }   
        }
  
        
    }
}