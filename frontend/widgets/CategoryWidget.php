<?php
namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Category;
use kartik\nav\NavX;
use backend\models\Catalog;
use common\Helpers\Partners;

class CategoryWidget extends Widget{
    function arrOrder()
    {
        $args = func_get_args();

        $data = array_shift($args);

        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

	public function run(){

        $categories = Category::find()->where(['active' => 1])->indexBy('id')->orderBy('id')->all();
        $_menuItems = $this->getMenuItems($categories);
        //arrOrder($result, "d_reg",SORT_DESC);
/*
        echo "<pre>";
        print_r($menuItems);
        echo "</pre>";
        exit;*/


        $count = count($_menuItems);
        $first = ceil($count/2);
     
        if(is_array($_menuItems)) {
            $_menuItems = $this->arrOrder($_menuItems,"sort",SORT_DESC);
            $step = 1;
            $i = 1;
            foreach ($_menuItems as $it) {
                $menuItems[$step][$it["id"]] = $it;
                if($i == $first){
                    $step++;
                }
                $i++;
            }
        }
        include "html.php";
                       
                      
       /* return NavX::widget([
            'options' => ['class' => 'nav nav-pills1 nav-mall'],
            'items' => ['items' => ['label' => '<span class="glyphicon glyphicon-th"></span> ' . Yii::t('shop', 'Shop'), 'options' => ['class' => 'buttonMall'], 'items' => $menuItems]],
            'activateParents' => true,
            'encodeLabels' => false
        ]);*/
	}

    private function getMenuItems($categories, $activeId = null, $parent = null, $mainCat = null)
    {
       // print_r("ssffssf");
        $menuItems = [];
        $defaultLng = "uk";
        $lngPrefix = "";
        if(\Yii::$app->language != $defaultLng){
            $lngPrefix ="/".\Yii::$app->language;
        }
        $idPartners = [];
        foreach ($categories as $category) {
            if ($category->id == 1669 || Partners::disabled($category->partner,$category)) {
                continue;
            }

            if ($category->parent_id === $parent) {
                $_slug = '/shop/catalog/list';
                $slug = $category->slug;
                $url = [$_slug, 'slug' => $slug, 'id' => $category->id];
                 if($category->catalog_id !== NULL && ( $link = Catalog::find()->where(["id" => $category->catalog_id])->select("translit")->one()) == true){

                    $url = $lngPrefix."/catalog/".$link->translit;
                }
                if($category->link != NULL){
                    $url = $lngPrefix.$category->link;
                }
                $title = $category->title;
                if(isset(Partners::$partners[$category->partner]) && $title != Partners::$partners[$category->partner]){


                    if(Partners::isAdmin() && ($cat = Category::find()->where(['slug' => strtolower($category->partner), 'partner' => $category->partner])->one()) == true
                            && $cat->id != $category->parent_id)
                        $title = Partners::getPrefixAdmin($category->partner).$title;

                }

                $currentMainCat = $mainCat ?? $category->id;

                $menuItems[$category->id] = [
                    "id" => $category->id,
                    "sort" => ($category->sort == NULL)?0:$category->sort,
                    'active' => $activeId === $category->id,
                    //'options' => ['data' => ['parent_id' =>$category->parent_id, 'id' =>$category->id]],
                    'label' => $title,
                    'url' => $url,
                    'items' => $this->getMenuItems($categories, $activeId, $category->id, $currentMainCat),
                    'parent_id' => $category->parent_id,
                    'partner' => $category->partner,
                    'mainCat' => $currentMainCat
                ];
            }

        }
        $menuItems = $this->arrOrder($menuItems,"sort",SORT_DESC);
        $_menuItems = [];
        foreach ($menuItems as $it){
            $_menuItems[$it["id"]] = $it;
        }
      //  print_r($idPartners);
        return $_menuItems;
    }
}
?>