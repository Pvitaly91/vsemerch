<?php

namespace backend\modules\shop\controllers;

use common\models\Product;
use Yii;
use common\models\Category;
use backend\modules\shop\models\CategorySearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\Catalog;
use yii\web\HttpException;
/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function getPageList(){
        $catalog = Catalog::find()->select("id,name_ru")->all();
        $result = [];
        foreach ($catalog as $key => $itm){
         //   if(isset($itm["id"]))
                $result[$itm["id"]] = $itm["name_ru"];
        }
       return $result;
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $id id of the parent category
     * @return mixed
     */

    public function actionCreate($id = null)
    {
        if(isset($_GET["parent_id"]) && $_GET["parent_id"] != "false" && ($cat =\common\models\Category::find()
                ->where(["id" =>$_GET["parent_id"]])
                ->andWhere(["is","parent_id",NULL])->one()) != true
                
         ){
            throw new HttpException(404, 'Данной странице не существует!');
        }
        $partner = null;
        if(isset($_GET["parent_id"])){
            $partner = "main";
            
        }
        
      //  print_r($this->getPageList());
        //ALTER TABLE `shop_category` ADD `catalog_id` INT NULL DEFAULT NULL AFTER `active`;
        //ALTER TABLE `shop_category` ADD `sort` INT NULL DEFAULT NULL AFTER `catalog_id`;
        $categories = Category::asArray();
        $model = new Category();
    
        if(!isset($_GET["refer"])) //BAG!
            $model->parent_id = $id;
        $pageList = $this->getPageList();
        /*echo "<pre>";
        print_r($pageList);
        echo "</pre>";
        exit;*/
      //  dd($_GET);
      
        if (($post = Yii::$app->request->post()) == true) {
           
            
          
            $model->load($post);
            $model->partner = (isset($partner))?$partner:NULL;
            
            if($model->save()){
                if($partner == "main"){
                   
                    $model->partner_id = $model->partner_slug = $model->slug;
                    $model->save(false);
                }
            //  dd(Yii::$app->request->post());
              $model->copyProductsWithCategory();
              if(isset($_GET["refer"])){

                  return $this->redirect($_GET["refer"]);  
              }else
                  return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $catName = '';
            if(is_object($cat)){
                $catName = $cat->title;
            }
            
          
    
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
                'pageList' => $pageList,
                "catNama" =>$catName,
                "partner" => $partner   
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $categories = Category::asArray();
        $model = $this->findModel($id);
        $pageList = $this->getPageList();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->copyProductsWithCategory();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
             $partner = $model->partner;
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
                'pageList' => $pageList,
                "partner" => $partner  
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Product::find()->where(['category_id' => $id])->exists()) {
            throw new BadRequestHttpException('В данной категории есть товары, поэтому в начале нужно удалить товары, а потом категорию!');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::find()->where(['id' => $id])->multilingual()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
