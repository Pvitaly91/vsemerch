<?php

namespace frontend\modules\shop\controllers;

use common\models\Category;
use common\models\CategoryTranslate;
use common\models\Product;
use common\models\ProductOption;
use common\models\ProductTranslate;
use frontend\modules\shop\widgets\FilterCheckboxWidget;
use yii\data\ActiveDataProvider;
use Yii;

use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ProductController extends \yii\web\Controller
{   
    use traitMakeSku;
    public $allowedTags = "<p><br><strong><ul><li><div><span><i>";
    public function actionView($slug, $id)
    {
       
        $model = Product::find()->where(['id' => $id,"not_active" => 0]);
   
        if(!\common\Helpers\Partners::isAdmin()){
            foreach (\common\Helpers\Partners::$disabled as $partner => $disabled){
                if($disabled == true){
                     $model->andWhere(["!=",Product::tableName() .".partner",$partner]); 
                }

            }         
        }
        $model = $model->one();

        if(!$model) {
            throw new NotFoundHttpException('Данного товара нет!');
        }

      //  dd($model->category_id);
       if(!\common\Helpers\Partners::isAdmin()){
            foreach (\common\Helpers\Partners::$partners as $partnerSlug => $partnerName) {
				$cat = Category::find()->where([
					"slug" => $partnerSlug,
					"partner" => $partnerSlug
				])->one();

				$productCat = Category::find()->where([
					"id" => $model->category_id,
					"partner" => $partnerSlug
				])->one();

				if (!$cat || !$productCat) {
					continue;
				}

				if ($productCat->parent_id && $cat->id == $productCat->id) {
					throw new NotFoundHttpException('Данного товара нет!');
				}
			}
       }
       // dd($mainPartnerCatId);
        //$mainPartnerCatId = Category::find()->where([""])  
        $model->description = strip_tags($model->description,$this->allowedTags);
        $canonical =  Url::canonical();
        $model->checkAvaiableSize(); 
        $skus = $this->makeSKU($model,$canonical);
        //
        $parentCategory = $model->category ? $model->category->parent : null;
        $prentCategoryName = $parentCategory ? $parentCategory->title : null;
      //  $model->normalizeFoto();
        return $this->render('view', [
            "canonical" => $canonical,
			"prentCategoryName" => $prentCategoryName,
            'model' => $model,
            "skus" => $skus,
            "active_id" => $id
        ]);
    }
    public function actionProductdata(){
        header('Content-Type: application/json; charset=utf-8');
        $result["success"] = false;
        if(isset($_GET["id"]) && ($id = $_GET["id"]) == true && ($model = Product::find()->where(['id' => $id,"not_active" => 0])->one()) == true){
            $this->layout = "empty";
            $model->checkAvaiableSize();
            $model->description = strip_tags($model->description,$this->allowedTags);
            $result["html"] =  $this->render('product_body', [
                'model' => $model,
            ]);
            $result["description"] = ($model->description)?$model->description:$model->title;
            $result["option"] = $this->render('option_block', [
                'model' => $model,
            ]);
             $result["gallery"] = $this->render('gallery_block', [
               'item' => $this->makeGallery($model),
            ]);
            $result["title"] =  $model->title;
            $result["success"] = true;
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    } 
}
