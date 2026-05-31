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
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use backend\models\Catalog;

class CatalogController extends \yii\web\Controller
{
    use traitMakeSku;
      
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Url::remember();
            return true;
        } else {
            return false;
        }
    }
    function getEmptyCategories($active = 1){
        $cats = Category::find()->andWhere(['>',"parent_id", '0'])->andWhere(['active' => $active])->all();
        //  $cats = Category::find()->andWhere(['active' => '1'])->all();
        //  d($cats);
       $res =[];
        foreach ($cats as $itm){
            //   print_r();
            //  $res[$itm->id] = 0;
             if(Product::find()
                ->andWhere(['=','category_id',$itm->id])
                ->andWhere(['<','not_active','1'])
                ->select('id')->count() == '0')
             {
                 $res[] = $itm->id;
             }
        }
        return $res;
    }
    function setActiveStatus($catId,$satus){
        $model = Category::findOne([ 'id' => $catId]);
        if($model != NULL){
            $model->active = $satus;
            $model->save(false);
        }
    }
    public function actionList($slug, $id)
    {
      
        $catMap = \common\models\CategoriesMap::find()->where(["partner_slug" => $slug])->one();
        $cat = Category::find()->where(["slug" => $slug])->one();
     // dd($cat->title);
     
        /** @var Category $category */

        /*  deactive empty categories
        foreach ($this->getEmptyCategories(1) as $catId){
            $this->setActiveStatus($catId,2);
        }*/

        if (!$model = Category::findOne(['slug' => $slug, 'id' => $id, "active" => 1])) {
            throw new NotFoundHttpException('Категории нет!');
        }
        if($model->catalog_id !== NULL){
            throw new HttpException(404, 'Данной странице не существует!');
        }

        if(!\common\Helpers\Partners::isAdmin() && isset(\common\Helpers\Partners::$disabled[$model->partner]) && \common\Helpers\Partners::$disabled[$model->partner] == true){
            throw new HttpException(404, 'Данной странице не существует!');
        }
        // dd($model);
        $sort = null;
        if(Yii::$app->request->get('sort')) {
            $sort = Yii::$app->request->get('sort');
        }

        $id = $model->id;
     //   dd($model->id);
        $category = null;

        $categories = Category::find()->indexBy('id')->orderBy('id')->all();
       
        $productsQuery = Product::find();
        if ($id !== null && isset($categories[$id])) {
            $category = $categories[$id];
            $productsQuery->where([Product::tableName() . '.category_id' => $this->getCategoryIds($categories, $id),"not_active" => 0]);
            if(!\common\Helpers\Partners::isAdmin()){
                foreach (\common\Helpers\Partners::$disabled as $partner => $disabled){
                    if($disabled == true){
                         $productsQuery->andWhere(["!=",Product::tableName() .".partner",$partner]); 
                    }
                   
                }
                  
            }
            
        }

        $productsQueryClone = clone $productsQuery;
        $filters_array = FilterCheckboxWidget::getListFilters($productsQueryClone);

        $filters = ProductOption::getFilters($productsQuery, $filters_array);

        foreach ($filters_array as $group => $filter) {
            $productsQuery->innerJoin(ProductOption::tableName() . " `po_{$group}`", Product::tableName() . ".id = `po_{$group}`.product_id");
            $productsQuery->andOnCondition(["`po_{$group}`.slug_option" => $group]);
            $productsQuery->andOnCondition(['IN', "`po_{$group}`.slug", $filter]);
            $productsQuery->andOnCondition(["`po_{$group}`.is_filter" => 1]);
      
        }
     
   
        if(($filterString = Yii::$app->request->get('filters')) != true || (($filterString = Yii::$app->request->get('filters')) == true && strpos($filterString, "kolir:") === false)){
          $productsQuery->andWhere(["=","is_main","1"]);
        }
     //  dd($productsQuery);
        $productsDataProvider = new ActiveDataProvider([
            'query' => $productsQuery,
            'pagination' => [
                'pageSize' => 21,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'class' => \common\components\Pagination::className(),
                
            ],
			 'sort' => [
              'defaultOrder' => [
                   'not_available' => SORT_ASC,
               ]
			]

        ]);
        $products = $productsDataProvider->getModels();
        $colors = [];
        
        foreach($products as $k => &$model){
            $canonical = false;
            if(($color = $this->makeSKU($model, $canonical, true)) == true){
                $model->checkAvaiableSize(); 
                $colors[$model->id] = $color;
               
                if($model->isAdmin() && isset(\common\Helpers\Partners::$partners[$model->partner])){
                    $model->title = strtoupper($model->partner)." ".$model->title;
                }
            }
          
        }
        //available filter map 
        $map =  $_map = [];
        foreach ($filters as $filter){
            foreach($filter['filters'] as $f){
                
            if(isset($_map[$f['slug_option']]) && !is_array($_map[$f['slug_option']]) || (isset($_map[$f['slug_option']]) && (!in_array($f->partner, $_map[$f['slug_option']]) ))){
                 $_map[$f['slug_option']][] = $f->partner;
            }
           
                $m = ($f->partner == '')?"main":$f->partner;
                $map[$f['slug_option']][$m] = $m;
            }
          
        }
       
        foreach($map as $n => $m){
            if(!isset($m["main"])){
                unset($map[$n]);
            }
        }
        foreach($_map as $k => $it){
            foreach($it as $slug =>$value){
                if($value == null){
                   unset($_map[$k]);
                }
            }
            if(isset($_map[$k]))
                $_map[$k] = $it[key($it)];
          //  $_map[$k] = $it[0];
        }
      
		$parentCategory = $category->parent;
        $prentCategoryName = $parentCategory ? $parentCategory->title : null;
		
        return $this->render('list', [
            'category' => $category,
            'filters' => $filters,
            'sort' => $sort,
            'productsQueryClone' => $productsQueryClone,
            'productsDataProvider' => $productsDataProvider,
			'prentCategoryName' =>  $prentCategoryName,
            'products' => $products,
            'colors' => $colors,
            'cat_id' => $id,
            "map" => $map,
            "_map" => $_map
        ]);
    }

    public function actionSearch($search_str)
    {
   
        $sort = null;
        if(Yii::$app->request->get('sort')) {
            $sort = Yii::$app->request->get('sort');
        }
      
        $productsQuery = Product::find();
        if(!empty($search_str)) {
            $productsQuery->joinWith('translate');
            $productsQuery->where(['like', 'title', $search_str]);
            $productsQuery->andWhere(["not_active" => 0]);
            $productsQuery->orWhere(['like', 'code', $search_str]);
            $productsQuery->andWhere(["not_active" => 0]);
            //if search by product code
            if(Product::find()->joinWith('translate')->where(['like', 'code', $search_str])->all()){
                $productsQuery = $productsQuery->orderBy("code ASC");
            } 
             if(!\common\Helpers\Partners::isAdmin()){
                foreach (\common\Helpers\Partners::$disabled as $partner => $disabled){
                    if($disabled == true){
                         $productsQuery->andWhere(["!=",Product::tableName() .".partner",$partner]); 
                    }
                   
                }
                  
            }
        }
        $productsDataProvider = new ActiveDataProvider([
            'query' => $productsQuery,
            'pagination' => [
                'pageSize' => 12,
                'pageSizeParam' => false,
                'forcePageParam' => false
            ],
        ]);
        $products = $productsDataProvider->getModels();
                $colors = [];

                foreach($products as $k => &$model){
                    $canonical = false;
                    if(($color = $this->makeSKU($model, $canonical, true)) == true){
                        $model->checkAvaiableSize(); 
                        $colors[$model->id] = $color;
                    }

                }
        return $this->render('search', [
            'sort' => $sort,
            'products' => $products,
            'colors' => $colors,
            'productsDataProvider' => $productsDataProvider,
        ]);
    }

    public function actionView()
    {
        return $this->render('view');
    }

    public function actionSearchAjax($search_str)
    {
        
        $categories = Category::find()->joinWith('translate')->where(['like', 'title', $search_str])->andWhere(["=","active","1"])->all();
        $products = Product::find()->joinWith('translate')
                ->where(['like', 'title', $search_str])
                ->andWhere(["not_active" => 0])
                ->orWhere(['like', 'code', $search_str])
                ->andWhere(["not_active" => 0]);
        
        //if search by product code
        
        if(Product::find()->joinWith('translate')->where(['like', 'code', $search_str])->all()){
            
            $products = $products->orderBy("code ASC");
        }        
        if(!\common\Helpers\Partners::isAdmin()){
            foreach (\common\Helpers\Partners::$disabled as $partner => $disabled){
                if($disabled == true){
                     $products->andWhere(["!=",Product::tableName() .".partner",$partner]); 
                }

            }         
        } 
                
        $products=   $products->limit(10)->all();
        $result = $r = [];
   
        if(is_array($categories)){
            foreach ($categories as $k => $cat){
              
                 if(isset($cat["catalog_id"]) &&  !empty($cat["catalog_id"])){
                        $Catalog = Catalog::find()->where(["=","id",$cat["catalog_id"]])->all();
                        $categories[$k]->catalog_id = $Catalog[0]->translit;
                 } 

            }
        }
                

        return $this->renderAjax('search-ajax', [
            'categories' => $categories,
            'products' => $products
        ]);
    }

    /**
     * @param Category[] $categories
     * @param int $activeId
     * @param int $parent
     * @return array
     */
    private function getMenuItems($categories, $activeId = null, $parent = null)
    {
        $menuItems = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parent) {
                $menuItems[$category->id] = [
                    'active' => $activeId === $category->id,
                    //'options' => ['data' => ['parent_id' =>$category->parent_id, 'id' =>$category->id]],
                    'label' => $category->title,
                    'url' => ['catalog/list', 'slug' => $category->slug],
                    'items' => $this->getMenuItems($categories, $activeId, $category->id),
                ];
            }
        }
        return $menuItems;
    }


    /**
     * Returns IDs of category and all its sub-categories
     *
     * @param Category[] $categories all categories
     * @param int $categoryId id of category to start search with
     * @param array $categoryIds
     * @return array $categoryIds
     */
    private function getCategoryIds($categories, $categoryId, &$categoryIds = [])
    {
        foreach ($categories as $category) {
            if ($category->id == $categoryId) {
                $categoryIds[] = $category->id;
            } elseif ($category->parent_id == $categoryId) {
                $this->getCategoryIds($categories, $category->id, $categoryIds);
            }
        }
        return $categoryIds;
    }
}
