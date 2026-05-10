<?php

namespace backend\modules\shop\controllers;

use common\models\Category;
use common\models\ProductOption;
use common\models\ProductOptionTranslate;
use common\models\ProductSize;
use common\models\Size;
use Yii;
use common\models\Product;
use backend\modules\shop\models\ProductSearch;
use common\components\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
    public function getSizetable($id){
        $pdoduct = Product::find()->select("sku_group")->where(['id' => $id])->one();
        $pdoducts = Product::find()->select("id")->where(['sku_group' => $pdoduct->sku_group])->all();
        $sizes = [];
        foreach($pdoducts as $prod){
         
            $porductSizes = productSize::find()->where(['product_id' => $prod->id])->all();
            foreach($porductSizes as $sizeModel){
                $size = Size::find()->where(['id' => $sizeModel->size_id])->one();
                $sizes[$size->name]["value"] = $sizeModel->params;
                $sizes[$size->name]["ids"][] = $sizeModel->id;
            }

        }
        return $sizes;
    }
    public function actionTableSize($id){
       
        $sizes = $this->getSizetable($id);
        $pdoduct = Product::find()->where(['id' => $id])->one();
      
        if(($post = Yii::$app->request->post()) == true){
           
            foreach($post as $fname => $value){
                if(isset($sizes[$fname])){
                   // dd([$size, $fname,$value]);
                    foreach($sizes[$fname]['ids'] as $sizeId){
                        $porductSizes = productSize::find()->where(['id' => $sizeId])->one();
                        $porductSizes->params = $value;
                        if($porductSizes->save()){
                            $sizes[$fname]["value"] = $value;
                        }
                     
                    }
                }    
            }
            Yii::$app->session->setFlash('success', "Таблица обновлена");
        }
     
        return $this->render("table-size",["sizes" => $sizes, "pdoduct" => $pdoduct]);
    }
    public function actionAutocompleteOption($term)
    {
        $model = ProductOptionTranslate::find()->where(['like', 'option', $term])->groupBy('option')->asArray()->all();
        $array = array_column($model, 'option');
        return json_encode($array);
    }

    public function actionAutocompleteValue($term)
    {
        $model = ProductOptionTranslate::find()->where(['like', 'value', $term])->groupBy('value')->asArray()->all();
        $array = array_column($model, 'value');
        return json_encode($array);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $categories = Category::asArray();
        $modelProduct = new Product();
        $modelsOption = [new ProductOption()];
        $modelsSize = [new ProductSize()];

        if ($modelProduct->load(Yii::$app->request->post())) {
            $modelProduct->is_main = '1';    
            $modelsOption = Model::createMultiple(ProductOption::classname());
            Model::loadMultiple($modelsOption, Yii::$app->request->post());

            $modelsSize = Model::createMultiple(ProductSize::classname(), $modelsSize);
            Model::loadMultiple($modelsSize, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsOption),
                    ActiveForm::validate($modelProduct),
                    ActiveForm::validate($modelsSize)
                );
            }

            // validate all models
            $valid = $modelProduct->validate();
            $valid = Model::validateMultiple($modelsOption) && Model::validateMultiple($modelsSize) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelProduct->save(false)) {
                        foreach ($modelsOption as $modelOption) {
                            $modelOption->product_id = $modelProduct->id;
                            if (!($flag = $modelOption->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        foreach ($modelsSize as $modelSize) {
                            if(empty($modelSize->price)) {
                                continue;
                            }
                            $modelSize->product_id = $modelProduct->id;
                            if (!($flag = $modelSize->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelProduct->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $modelProduct,
            'modelsOption' => (empty($modelsOption)) ? [new ProductOption] : $modelsOption,
            'categories' => $categories,
        ]);

    }
    function getPhotos(&$item){
        $photos = [];
        $basePath = $_SERVER["DOCUMENT_ROOT"]."/frontend/web";
        if($item->getPic('image', 'thumb'))
            $photos[] = $basePath.$item->getPic('image', 'thumb');
        
        if($item->getPic('image', 'preview'))
            $photos[] = $basePath.$item->getPic('image', 'preview');
     
      
        foreach($item->images as $image){

            $photos[] = $basePath.$image->getThumbFileUrl('image', 'ico');
            $photos[] = $basePath.$image->getImageFileUrl('image');
            $photos[] = $basePath.$image->getThumbFileUrl('image', 'thumb');
          
        }
        return $photos;
    }
    function delPhotos(&$modelProduct){
       // dd($modelProduct->image);
        $modelProduct->image = NULL;
        if($modelProduct->save(false)){
             $basePath = $_SERVER["DOCUMENT_ROOT"]."/frontend/web";
            if(file_exists($basePath."/upload/shop/products/".$modelProduct->id.".jpg")){
                unlink($basePath."/upload/shop/products/".$modelProduct->id.".jpg");
            }
             if($modelProduct->getPic('image', 'thumb') && file_exists($basePath.$modelProduct->getPic('image', 'thumb'))){
                  unlink($basePath.$modelProduct->getPic('image', 'thumb'));
             }
              if($modelProduct->getPic('image', 'preview') && file_exists($basePath.$modelProduct->getPic('image', 'preview'))){
                  unlink($basePath.$modelProduct->getPic('image', 'preview'));
             }
        };
         foreach($modelProduct->images as $image){
             $image->delete();
          //   $image->delete();
         }
     /*  $photos = $this->getPhotos($modelProduct);
   
        if(is_array($photos) && !empty($photos)){
            foreach ($photos as $photo){
              
                if(file_exists($photo)){
                    unlink($photo);
                }
            }
        }*/
     //   exit;
    }
 
    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(isset($_GET["delphotos"]) && ($modelProduct = $this->findModel($_GET["delphotos"])) == true){
            $this->delPhotos($modelProduct);
            Yii::$app->session->setFlash('success', "Фото удалени.");
            return  $this->redirect(['update', 'id' => $_GET["id"]]);
        }
        
        $categories = Category::asArray();
        $modelProduct = $this->findModel($id);
        $modelsOption = $modelProduct->optionTranslate;
        $modelsSize = $modelProduct->size;
        
        if ($modelProduct->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsOption, 'id', 'id');
            $modelsOption = Model::createMultiple(ProductOption::classname(), $modelsOption);
            Model::loadMultiple($modelsOption, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsOption, 'id', 'id')));

            $oldModelsSize = $modelsSize;
            $modelsSize = Model::createMultiple(ProductSize::classname(), $modelsSize);
            Model::loadMultiple($modelsSize, Yii::$app->request->post());
            foreach ($oldModelsSize as $oldModelSize) {
                foreach ($modelsSize as $modelSize) {
                    if($oldModelSize->id == $modelSize->id && empty($modelSize->price)) {
                        $deletedSizeIDs[] = $oldModelSize->id;
                    }
                }
            }
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsOption),
                    ActiveForm::validate($modelProduct),
                    ActiveForm::validate($modelsSize)
                );
            }

            // validate all models
            $valid = $modelProduct->validate();
            $valid = Model::validateMultiple($modelsOption) && Model::validateMultiple($modelsSize) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelProduct->save(false)) {
						/////////////////////////////
                         if($modelProduct->transfer_all == true 
                          //  && $old_cat != $modelProduct->category_id 
                            && ($products = Product::find()->where(["=","sku_group",$modelProduct->sku_group])->all()) == true
                            && is_array($products))
                        {
                             
                            foreach($products as $product){
                                $product->category_id = $modelProduct->category_id;
                                $product->save(false);
                            }

                        }
                        ////////////////////////////
                        if (!empty($deletedIDs)) {
                            ProductOption::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsOption as $modelOption) {
                            $modelOption->product_id = $modelProduct->id;
                            if (!($flag = $modelOption->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
//                        if (!empty($deletedSizeIDs)) {
//                            ProductSize::deleteAll(['id' => $deletedSizeIDs]);
//                        }
//                        foreach ($modelsSize as $modelSize) {
//                            if(empty($modelSize->price)) {
//                                continue;
//                            }
//                            $modelSize->product_id = $modelProduct->id;
//                            if (!($flag = $modelSize->save(false))) {
//                                $transaction->rollBack();
//                                break;
//                            }
//                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelProduct->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $modelProduct,
            'modelsOption' => (empty($modelsOption)) ? [new ProductOption()] : $modelsOption,
            'categories' => $categories,
        ]);

    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        foreach ($model->images as $image) {
            $image->delete();
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::find()->where(['id' => $id])->multilingual()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
