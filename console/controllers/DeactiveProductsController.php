<?php
namespace console\controllers;

use console\components\Controller;
use console\models\Product;
use common\models\Category;

class DeactiveProductsController extends Controller{

    function getEmptyCategories($active = 1){
        $cats = Category::find()->andWhere(['>',"parent_id", '0'])->andWhere(['active' => $active])->all();
        //  $cats = Category::find()->andWhere(['active' => '1'])->all();
        //  d($cats);
        $catsExt = Category::find()->andWhere(['>',"Catalog_id", '0'])->orWhere(['not', ['link' => null]])->all();
        $_catsExt = $res = [];
        foreach ($catsExt as $k => $itm){
            $_catsExt[$itm->id] = $itm->id;
        }

        foreach ($cats as $itm){
            //   print_r();
            //  $res[$itm->id] = 0;
            if(Product::find()
                    ->andWhere(['=','category_id',$itm->id])
                    ->andWhere(['<','not_active','1'])
                   //->andWhere(['=','partner',"bergamo"])
                    ->select('id')->count() == '0'
                && !isset($_catsExt[$itm->id])
            )

            {
                $res[$itm->id] = $itm->id;
            }
        }
   //     print_r(count($_catsExt)); echo"\n";
  //      print_r(count($res)); echo"\n";
   //     exit;
        return $res;
    }
    function setActiveStatus($catId,$satus){
        $model = Category::findOne([ 'id' => $catId]);
        if($model != NULL){
            $model->active = $satus;
            $model->save(false);
        }
    }

    function actionIndex(){

       /* $xml_file = file_get_contents('http://totobi.com.ua/yml_get/lg3bjy2gvww');
        $xml = new \SimpleXMLElement($xml_file);
        $date = (array)$xml->attributes()->date;
        $date = $date[0];
        $date_catalog = strtotime($date);
        $models = Product::find()->where(['partner' => "totobi"])->andWhere(['not_available' => '0'])->andWhere(["<","date_catalog", $date_catalog])->all();

        foreach ($models as $model){

           $model->not_available = '1';
            $model->save(false);
        }
        exit;*/
       //print_r(strtotime($date));

    //   print_r(count($models));
    //   exit;
     //   $date_catalog = strtotime($date);

        /*  deactive empty categories
            deactive-products
        */
        if(true){
            foreach ($this->getEmptyCategories(1) as $catId){ //2 active 1 deactive
                $this->setActiveStatus($catId,2); //1 active 2 deactive
            }
        }



       if(false) {
           $models = Product::find()->where(['partner' => "bergamo"])->all();
           foreach ($models as $model) {
               $model->not_active = "1";
               $model->save(false);
           }
           echo count($models);
       }
    }
}