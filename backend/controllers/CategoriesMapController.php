<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\CategoriesMap;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\HttpException;
use common\models\Category;
use common\models\Product;

class CategoriesMapController extends Controller
{
    protected $types = ["subcat","merge"];
    /**
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

    /**
     * Lists all CategoriesMap models.
     * @return mixed
     */
    public function actionIndex()
    {
       /* include $_SERVER["DOCUMENT_ROOT"]."/console/controllers/includes/coincidenceArray.php";
       
       // dd($coincidence);
        foreach ($coincidence as $partnerSlug => $data) {
            $site_slug = $data['slug'];
            $type = $data['type'];
            $partner = "eney";
            // Find the matching record in the CategoriesMap model by partner_slug and partner
            $existingRecord = CategoriesMap::findOne(['partner_slug' => $partnerSlug, 'partner' => $partner]);

            if ($existingRecord === null) {
                // No existing record found, insert a new record
                $newRecord = new CategoriesMap();
                $newRecord->partner_slug = $partnerSlug;
                $newRecord->site_slug = $site_slug; // Assuming site_slug is the same as partner in this case
                $newRecord->partner = $partner;
                $newRecord->type = $type;
                $newRecord->created_at = date('Y-m-d H:i:s');
                $newRecord->save();
            } else {
                // Existing record found, update if necessary
                $existingRecord->type = $type;
                $existingRecord->save();
            }
        }*/
    }
    function actionDelete() {
        if (!isset($_GET["id"])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $id = $_GET["id"];
        
        $categoryMap = \common\models\CategoriesMap::find()->where(["=", "id", $id])->one();
       
        $category = \common\models\Category::find()->where(["=", "slug", $categoryMap->partner_slug])->one();
        
        $products_partners = Product::find()->where(["=", "category_id", $categoryMap->category_id])
                ->andWhere(["=", "partner", $optionMap->partner])
                ->all();
      
        foreach ($products_partners as $pModel) {
            foreach ($pModel->option as $option) {
                if ($option->slug_option == $optionMap->site_slug) {

                    $optionModel = \console\models\ProductOption::findOne($option->id);

                    //  dd($optionModel);
                    $optionModel->slug_option = $optionMap->partner_slug;
                    
                    $optionModel->save(false);
                   
                    break;
                }
            }
        }
        $optionMap->delete();
    }
    /**
     * Displays a single CategoriesMap model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CategoriesMap model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    
        if(!isset($_GET["type"]) || !in_array($_GET["type"], $this->types) && !isset($_GET["id"])){
            throw new HttpException(404, 'Данной странице не существует!');
        }else{
            $type = $_GET["type"];
            $id = $_GET["id"];
        }
        
        $models = Category::find()
            ->where(['in','partner' , \common\Helpers\Partners::$_main]) 
            ->andWhere(["active" => 1])  
            ->all();
        
        $partnerCategory = Category::find()
            ->where(['=','id' , $id])
            ->one();
        
      
        $cats = [];
        $catsBase = [];
        $kk = [];
        $nullParent = [];
        $mainCatsId = [];
        foreach($models as $k => $model){
            if($model->link != false || $model->catalog_id != false){
                unset($models[$k]);
                continue;
            }
            if($model->parent_id == NULL){
                $mainCatsId[$model->slug] = $model->id;
                $kk[] = $model->title;
                $cats[$model->id] = [
                    "slug" =>$model->slug,
                   // "name" => $model->title
                ];
                $nullParent[$model->slug] = $model->slug;
                unset($models[$k]);
            }
             $catsBase[$model->slug] = [
                 "id" => $model->id,
                 "title" => $model->title
             ];
        }
      //  dd($catsBase);
    //   dd($kk);
        $menu = [];
        $kk =[];
      
        foreach($models as $k => $model){
            if(isset($cats[$model->parent_id])){
                $menu[$cats[$model->parent_id]["slug"]][$model->slug] = $model->slug."-".$model->id;
               unset($nullParent[$cats[$model->parent_id]["slug"]]);
            } $kk[] = $model->title." ".$model->parent_id;
           
        }
        foreach($nullParent as $cat){
          $menu[$cat] = [];
        }
       
        //dd($kk);
       
        if(!$model =  CategoriesMap::find()
                ->where(['=','partner_slug' , $partnerCategory->slug])
                ->andWhere(['partner' => $partnerCategory->partner])        
                ->one())
        {
            $model = new CategoriesMap();
        }

       
        if (($site_slug = Yii::$app->request->post('site_slug')) == true ) {
            $post['partner_slug'] = $partnerCategory->slug;
            $post['site_slug'] = $site_slug;
            $post['partner'] = $partnerCategory->partner;
            $post['created_at'] = date('Y-m-d H:i:s');
            $post["type"] = $type; 
            
            $model->setAttributes($post);
                    
            if( $model->validate() && $model->save(false) && isset($catsBase[$post['site_slug']])){
               // dd($catsBase[$post['site_slug']]);
               // dd(\common\Helpers\Partners::getMainCategoryBySlug($post['site_slug'])->id);
                if($type == "subcat"){
                      
                    $partnerCategory->parent_id =  $catsBase[$post['site_slug']]['id'];
                    if($partnerCategory->save(false,null,true)){    
                        Yii::$app->session->setFlash('success', "Категория перемещена"); 
                         $products = Product::find()
                                 ->where(['partner' => $partnerCategory->partner])
                                 ->andWhere(['category_id' => $partnerCategory->id])
                                 ->all();
                        if(is_array($products)){
                            foreach($products as $prod ){
                                $prod->morePhotos();
                           } 
                        }
                    }
                }elseif($type == "merge"){
                    $partnerCategory->active =  0;
                    if($partnerCategory->save(false,null,true)){
                         $products = Product::find()
                                 ->where(['partner' => $partnerCategory->partner])
                                 ->andWhere(['category_id' => $partnerCategory->id])
                                 ->all();
                        if(is_array($products)){
                            
                           foreach($products as $prod ){
                               $prod->category_id = $catsBase[$post['site_slug']]['id'];
                               if($prod->save()){
                                   $prod->morePhotos();
                               }
                           } 
                        } 
                        Yii::$app->session->setFlash('success', "Товары категории перемещены"); 
                    }   
                        
                }
               
            }else{
               // dd($model->getErrors());
            }
       
        }
   
        return $this->render('create', [
            'model' => $model,
            'menu' => $menu,
            'partnerCategory' => $partnerCategory,
            'catsBase' => $catsBase,
            "mainCatsId" => $mainCatsId,
            'type' =>$type
        ]);
    }

    
    /**
     * Finds the CategoriesMap model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return CategoriesMap the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CategoriesMap::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
