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

class OptionsMapController extends Controller {
    //protected $types = ["subcat","merge"];
    /**
     * {@inheritdoc}
     */
    /* public function behaviors()
      {
      return [
      'verbs' => [
      'class' => VerbFilter::class,
      'actions' => [
      'delete' => ['POST'],
      ],
      ],
      ];
      } */

    /**
     * Lists all CategoriesMap models.
     * @return mixed
     */
    public function actionIndex() {
        
    }

    function actionDelete() {
        if (!isset($_GET["id"])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $id = $_GET["id"];
        $optionMap = \common\models\OptionMap::find()->where(["=", "id", $id])->one();
        $products_partners = Product::find()->where(["=", "category_id", $optionMap->category_id])
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
     * Creates a new CategoriesMap model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        /*if (false) {

            $optionMap = [
                "color" => "kolir",
                "density" => "silnist",
                "brandingtype" => "grupa-nanesenna",
                "trademark" => "tm",
            ];
            foreach ($optionMap as $partner_slug => $site_slug) {
                $model = new \common\models\OptionMap();
                $model->partner_slug = $partner_slug;
                $model->site_slug = $site_slug;
                $model->partner = "eney";
                $model->created_at = date('Y-m-d H:i:s');
                $model->save(false);
            }
        }*/
        if (!isset($_GET["slug"]) || ($slug = $_GET["slug"]) != true || !isset($_GET["catId"]) || !isset($_GET['partner'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $categoryId = $_GET["catId"];

        $partner = $_GET['partner'];

        if (Yii::$app->request->isPost && ($site_slug = Yii::$app->request->post("site_slug")) == true) {
            // dd([$site_slug,$slug]);
            if (!$optionMap = \common\models\OptionMap::find()
                    ->where(["=", "partner_slug", $slug])
                    ->andWhere(["partner" => $partner])->one())
                $optionMap = new \common\models\OptionMap();


            $optionMap->partner = $partner;
            $optionMap->site_slug = $site_slug;
            $optionMap->category_id = $categoryId;
            $optionMap->partner_slug = $slug;
            $optionMap->created_at = date('Y-m-d H:i:s');
            if ($optionMap->save(false)) {
                $products_partners = Product::find()->where(["=", "category_id", $categoryId])->andWhere(["=", "partner", $partner])->all();

                foreach ($products_partners as $pModel) {
                    foreach ($pModel->option as $option) {
                        if ($option->slug_option == $slug) {

                            $optionModel = \console\models\ProductOption::findOne($option->id);

                            //  dd($optionModel);
                            $optionModel->slug_option = $site_slug;
                            //   dd($optionModel);
                            // dd($optionModel);
                            $optionModel->save(false);
                            // dd($optionModel);
                            break;
                        }
                    }
                }
                Yii::$app->session->setFlash('success', "Готово");
            }
        }
        $products = Product::find()
                ->where(["=", "category_id", $categoryId])
                ->andWhere(["in", "partner", \common\Helpers\Partners::$_main])
                ->all();

        $productOption = \common\models\ProductOption::find()->where(["slug_option" => $slug])->one();

        $name = $products[0]->getPartnerPropertyTrans($productOption->option);
        $list = [];
        //   dd($name);
        foreach ($products as $item) {
            foreach ($item->option as $option) {
                $list[$option->slug_option] = $option->translation->option;
            }
        }

        $cat = Category::find()->where(["=", "id", $categoryId])->one();
        return $this->render('index', [
                    'list' => $list,
                    'name' => $name,
                    "catName" => $cat->title,
                    'partnerName' => $partner
        ]);
    }
}
