<?php
namespace console\controllers\traits;

use console\components\Controller;
use console\models\ProductOption;
use console\models\ProductOptionTranslate;
use console\models\Image;
use console\models\ImageTranslate;
use console\models\Category;
use console\models\CategoryTranslate;
use console\models\ProductTranslate;
use console\models\ProductSize;
use console\models\Size;
use common\models\Product;
use yii\helpers\Inflector;
use common\models\Language;
use Yii;
use PHPThumb\GD;
use yii\helpers\FileHelper;
use common\models\CategoriesMap;
use common\models\Category as ModelsCategory;
use common\models\ProductMap;


trait Common{
    
    public $translates = [
             "color" => [
                 "uk" => "Колір",
                  "ru" => "Цвет",
             ],
              "proizvoditel" => [
                 "uk" => "Виробник",
               "ru" => "Производитель",
             ],
             "brand" => [
                 "uk" => "TM",
                 "ru" => "TM",
             ],
            "razmer" => [
                 "uk" => "Розмір",
                 "ru" => "Размер",
             ],
            "grouppananeseniya" => [
                 "uk" => "Група нанесення",
                "ru" => "Группа нанесения",
             ],
            "razmernaneseniya" => [
                 "uk" => "Розмір нанесення",
                  "ru" => "Размер нанесения",
             ],
            "dimensions" => [
                 "uk" => "Розмір",
                "ru" => "Размер",
             ],  
             "category"=> [
                 "uk" => "Стать",
                  "ru" => "Пол",
             ], 
             "sex"=> [
                "uk" => "Стать",
                "ru" => "Пол",
            ], 
            "country_of_manufacture" => [
                "uk" => "Країна виробник",
                "ru" => "Страна производитель",
            ]
         ];
    
     public $modelMap = [
            "code",
            "price",
            'partner_id',
            "slug",
            "image",
            "is_main",
          //  "sku_group",
            "not_available"
        ];
    public $translateMap = [
        "title",
        "meta_title",
        "meta_description",
        "description"
    ];
    public $pCatId;
    public $savedProducts = []; 
    public $optionMap; 

    protected $ftpConect;
    protected $ftpLogin;
     /*////////////
    protected $thumbs = [
        'thumb' => ['width' => 300, 'height' => 300],
        'preview' => ['width' => 400, 'height' => 400],
    ];
    protected $ico = [
        'ico' => ['width' => 100, 'height' => 100],
        'thumb' => ['width' => 400, 'height' => 400],
    ];
    *////
    function ftpConnect(){


        $ftp_server = '176.9.83.91';
        $ftp_username = getenv('ALL_IMAGES_FTP_USER') ?: 'all_images';
        $ftp_password = getenv('ALL_IMAGES_FTP_PASSWORD') ?: '';

        // Connect to the FTP server
        $this->ftpConect = ftp_connect($ftp_server);

        if ($this->ftpConect) {
            // Login to the FTP server
            $this->ftpLogin = ftp_login($this->ftpConect, $ftp_username, $ftp_password);
            ftp_pasv($this->ftpConect, true);
        }    
    }
    function getCatModel($slug) {
        if (($model = Category::find()->where(['slug' => $slug, 'partner' => $this->partnerName])->one()) == true) {
            return $model;
        }
    }
    function getSiteCatBySlug($slug){
        if (($models = Category::find()->where(['slug' => $slug])->all()) == true) {
            foreach($models as $model){
                if($model->partner == \common\Helpers\Partners::$main || $model->partner == NULL)
                   return $model; 
            }
            
        }
    }
    function getSiteCatIdBySlug($slug){
        if(($model = $this->getSiteCatBySlug($slug)) == true){
            return $model->id;
        }
    }
    function get_http_response_code($domain1) {

        $headers = get_headers($domain1, false, stream_context_create($this->arrContextOptions));

        return substr($headers[0], 9, 3);
    }
    function getCatId($slug) {
        if (($model = $this->getCatModel($slug)) == true) {
            return $model->id;
        }
    }
    function getProductsModels() {
        $products = Product::find()->where(['partner' => $this->partnerName])->all();
        return $products;
    }

    function delAll() {
        foreach ($this->getProductsModels() as $model) {
            $model->delete();
        }
    }
    function createPartnerMainCat() {
       
        if ($this->getCatId(Inflector::slug($this->partnerName)) == null) {
            $model = new Category();
            $model->slug = $model->partner_slug = $model->partner = $this->partnerName;
            $model->active = "1";
            $model->save(false);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $model->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $model->id;
                $modelTranslate->language = $language;
                $modelTranslate->meta_description = $modelTranslate->seo = $modelTranslate->meta_title = $modelTranslate->title = \common\Helpers\Partners::$partners[$this->partnerName];

                $modelTranslate->save(false);
            }
        }
    }
   
    function dellAllCats() {
        if (($models = Category::find()->where(['partner' => $this->partnerName])->all()) == true) {
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }
    function deleteEmptyCats(){
        $models = Category::find()
                ->where(['partner' => $this->partnerName])
                ->andWhere(["not",["parent_id" => null]])
                ->all();
        
        if(is_array($models) && !empty($models)){
            foreach($models as $model){
                if(count(Product::find()
                        ->where(['partner' => $this->partnerName, 'category_id' => $model->id])
                        ->all()) == 0)
                {
                    $model->active = 0;
                    $model->save(false);
                   // echo $model->id."\r\n";
                }
            }
        }
    }
    
    function setTranslations(&$product,$id){
         foreach (Language::getLanguagesAsArray() as $language) {
            if (!$modelTranslate = ProductTranslate::findOne(['shop_product_id' => $id, 'language' => $language])) {
                $pFlag = 1;
                $modelTranslate = new ProductTranslate();
            }else{
                $pFlag = 0;
            }
          //  $pFlag = 1;
            $modelTranslate->shop_product_id = $id;
            $modelTranslate->language = $language;
            if($pFlag == 1){
                foreach($this->translateMap as $fName){
                    if(isset($product[$fName."_".$language]))
                        $modelTranslate->$fName = $product[$fName."_".$language];
                    else
                        $modelTranslate->$fName = $product[$fName];
                }
              //  $modelTranslate->title = $product["title"];
               // $modelTranslate->meta_title = $product["meta_title"];
              //  $modelTranslate->meta_description = $product["meta_description"];
              //  $modelTranslate->description = $product["description"];
            }
            $modelTranslate->save(false);
        }
        
    }
    function getOptionMap($value){
      
     //   print_r($this->optionMap);
      //  exit;
      //  return $value; 
      //echo $value."\r\n";
        return (isset($this->optionMap[$value]))? $this->optionMap[$value] : $value;
        
    }
    function getOptionValuesMap($value){
        $map = [
            "Жіноча" => "для жінок",
            "Чоловіча" => "для чоловіків"
        ];
        return (isset($map[$value]))?$map[$value]:$value;  
    }
    function initMap(){
        $models = \common\models\OptionMap::find()->where(["=","partner",$this->partnerName])->all();
        foreach ($models as $item){
            $this->optionMap[$item->partner_slug] = $item->site_slug;
        }
        $this->optionMap["grouppananeseniya"] = "grupa-nanesenna";
        $this->optionMap["category"] = "stat";
        $this->optionMap["sex"] = "stat";
        $this->optionMap["brand"] = "tm";
        $this->optionMap["color"] = "kolir";
        $this->optionMap["countryofmanufacture"]  = "country";  
        $this->pCatId = Category::find()->where(["partner" => $this->partnerName,"slug" =>$this->partnerName])->one()->id;
        
    }
    
    function setAttributes(&$product, $id){
      //  print_r($product);
        $delOption = false;
        foreach ($product["options"] as $option => $value) {
            $slugOption = $this->getOptionMap(Inflector::slug($option));
            if($delOption == false){
                if (!$modelProductOption = ProductOption::find()->where(['product_id' => $id, 'slug_option' => $slugOption, 'slug' => Inflector::slug($value)])->one()) {
                    $modelProductOption = new ProductOption();
                }
            }elseif($delOption === true){
                if (($modelProductOption = ProductOption::find()->where(['product_id' => $id, 'slug_option' => $slugOption, 'slug' => Inflector::slug($value)])->one()) == true) {
                    $modelProductOption->delete();
                }
                $modelProductOption = new ProductOption();
            }
           
          //  print_r($option); echo " "; print_r($this->getOptionMap(Inflector::slug($option))); echo " "; print_r($this->getOptionValuesMap($value)); echo "\r\n";
            $modelProductOption->product_id = $id;
            $modelProductOption->is_filter = true;
            $modelProductOption->slug_option = $slugOption;
            $modelProductOption->slug = Inflector::slug($this->getOptionValuesMap($value));
            $modelProductOption->partner = $this->partnerName;
          
           
            $modelProductOption->save(false);
         //   if($option == "country_of_manufacture"){
        //       print_r($modelProductOption);
         //       exit;
        //    }
            $ids[] = $modelProductOption->id;
            $_value =$value;
            foreach (Language::getLanguagesAsArray() as $language) {
                
              
                if(isset($product["options_".$language][$option])){
                    $value = $product["options_".$language][$option];
                }else{
                    $value = $_value;
                }
              
                if($delOption == false){
                    if (!$modelProductOptionTranslate = ProductOptionTranslate::findOne(['shop_product_option_id' => $modelProductOption->id, 'language' => $language])) {
                        $modelProductOptionTranslate = new ProductOptionTranslate();
                    }
                }else{
                    if (($modelProductOptionTranslate = ProductOptionTranslate::findOne(['shop_product_option_id' => $modelProductOption->id, 'language' => $language])) == true) {
                        $modelProductOptionTranslate->delete();
                    }
                    $modelProductOptionTranslate = new ProductOptionTranslate();
                }
                $modelProductOptionTranslate->shop_product_option_id = $modelProductOption->id;
                $modelProductOptionTranslate->language = $language;
                $modelProductOptionTranslate->option = (isset($this->translates[$option][$language]))?$this->translates[$option][$language]:$option;
                $modelProductOptionTranslate->value = str_replace("м²", "м2", $this->getOptionValuesMap($value));

                $modelProductOptionTranslate->save(false);
            }
        }

        if (isset($ids) && count($ids)) {
            ProductOption::deleteAll([
                'AND',
                'product_id=:product_id',
                ['not in', 'id', $ids]
                    ],
                    [':product_id' => $id]
            );
        }  
          
    }
    
    
    
    function setProduct($product){
	//	print_r($product); exit;
        $flag = 0;
        if (!$model = Product::find()->where(['partner' => $this->partnerName, 'partner_id' => $product['partner_id']])->one()) {
            $model = new Product();
            $flag = 1;
        }
        if($flag == 0){
          //  echo $model->id."\r\n";
        }
        foreach ($this->modelMap as $fName){
			if(isset($product[$fName])){
				$model->$fName = $product[$fName];
			}
            
        }
        if($flag == 1){
           $model->category_id = $product["category_id"]; 
           $model->sku_group = $product["sku_group"];
           $model->not_active = "0";
        }
        $model->partner = $this->partnerName;
        if(isset($product["more_photo"]) && $product["more_photo"]){
            $model->more_photos = json_encode($product["more_photo"]);
        }
        if($model->save(false)){
        // isnsert product photos
        //   echo $model->id."\r\n";
            $this->savedProducts[] = $model->id;
            if($this->loadPics == true && isset($product["image"]) &&  $product["image"] != "" && $model->id){
               // var_dump($product["image"]);
            
                $this->setPicture($product["image"], $model->id);
              
                $parent_cat = Category::find()->where(["id"=>$model->category_id,"parent_id" =>$this->pCatId])->one();


               if(isset($product["more_photo"]) && $product["more_photo"] && $parent_cat == NULL){
                    $this->setMorePhotos($product["more_photo"], $model->id);
                }
            }
            $this->setTranslations($product,$model->id);
            $this->setAttributes($product, $model->id);
            if(isset($product["sizes"])){

                $this->setSizes($product["sizes"],$model->id);
            }

        }else{
            print_r($product);
            exit;
        }
       /* $model->code = $product["code"];
        $model->price = $product["price"];
        $model->partner_id = $product['partner_id'];
        $model->category_id = $product['category_id'];
        $model->slug = $product["slug"];
        $model->image = $product["image"];
        $model->is_main = $product["is_main"];
        $model->sku_group = $product["sku_group"];
        $model->not_available = $product["not_available"];*/
       
       
       
    }
    function getCategoryIdByMap($partner_id){
        if(($producMapModel = ProductMap::find()->where(['partner_product_id' => $partner_id, 'product_partner' => $this->partnerName])->one())== true){
            $category = Category::find()->where(["partner_id" => $producMapModel->category_partner_id,"partner" => $producMapModel->category_partner])->one();
            return $category->id;
        }
    }
    function getCategoryMapMerge($slug){
        if(
            $catMap = CategoriesMap::find()
                ->where(['=','partner' , $this->partnerName])
                ->andWhere(['=',"partner_slug",$slug])
                ->andWhere(['=',"type","merge"])        
                ->one()
        )
        {
            return $this->getSiteCatIdBySlug($catMap->site_slug);
        }
           
    }

    function makeCats(){
        $parentMaimId =  $this->getCatId($this->partnerName);
        
        foreach($this->data as $cat){
            $cat["cat"]["parent_id"] = $parentMaimId;
     
            $catId = $this->setCategory($cat["cat"]);
           // print_r($cat["products"]);
          //  exit;
            foreach($cat["products"] as $product){
                if(($categoryId = $this->getCategoryIdByMap($product['partner_id'])) == true){
                    $product["category_id"] = $categoryId;
                }elseif(($categoryId = $this->getCategoryMapMerge($cat["cat"]["slug"])) == true){
                    $product["category_id"] = $categoryId;
                }else{
                    $product["category_id"] = $catId;
                }
               
                $this->setProduct($product);
            }
        }
        $products = Product::find()->where(["not in","id",$this->savedProducts])->andWhere(["partner" => $this->partnerName])->all();
      
        if(is_array($products)){
            foreach($products as $prod){
                $prod->not_available = 1;
            //    echo $prod->code."\r\n";
                $prod->save(false);
            }
        }
    }
     function getCategoryModel($slug){
        if (!$modelCategory = Category::find()->where(['partner_slug' => $slug, 'partner' => $this->partnerName])->one()) {
            $modelCategory = new Category();
        }
        return $modelCategory;
    }
    function setCategory($cat){
        $modelCategory = $this->getCategoryModel($cat["slug"]);
     //  print_r($cat);
     //  exit;
        $translates = $cat['translates'];
      
        unset($cat['translates']);
        foreach($cat as $field => $value){
            $modelCategory->$field = $value;
        }
        
        $catMap = CategoriesMap::find()->where(['=','partner' , $this->partnerName])->andWhere(['=',"partner_slug",$modelCategory->slug])->one();
            if($catMap != NULL && $catMap->type == "subcat"){
              
                $modelCategory->parent_id = $this->getSiteCatIdBySlug($catMap->site_slug);
            }
                
        $modelCategory->save(false);
        foreach (Language::getLanguagesAsArray() as $language) {
            if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $modelCategory->id, 'language' => $language])) {
                $modelTranslate = new CategoryTranslate();
            }

            $translates["shop_category_id"] = $modelCategory->id;
            $translates["language"] = $language;
            if(!isset($translates["seo"]))
                $translates["seo"] = " ";
          // print_r($translates);
         //  exit;
            foreach($translates as $field => $value){
                
                $modelTranslate->$field = $value;
            }
            $modelTranslate->save(false);
        }
        return $modelCategory->id;
    }  
    function setSizes($sizes,$id){
      
        if (is_array($sizes) && !empty($sizes)) {
             /* 
               "name",
               "product_id", 
               "not_available",
                "price"
               "code"
              
              *                */
            foreach($sizes as $size){
                if($size["name"] == ""){
                    //print_r($id);
                    //print_r($size);
                    //exit;
                    continue;
                }
                if (!$modelSize = Size::find()->where(['name' => $size["name"]])->one()) {
                    $modelSize = new Size();
                }

                $modelSize->slug = Inflector::slug($size["name"]);
                $modelSize->name = $size["name"];
                $modelSize->save(false);

                if (!$modelProductSize = ProductSize::find()->where(['product_id' => $id, 'size_id' => $modelSize->id])->one()) {
                    $modelProductSize = new ProductSize();
                }


                $modelProductSize->not_available = $size["not_available"];
                $modelProductSize->product_id = $id;
                $modelProductSize->size_id = $modelSize->id;
                $modelProductSize->price = $size["price"];
                $modelProductSize->code = $size["code"];
				if(isset($size["params"]))
					$modelProductSize->params = $size["params"];
              //  $modelProductSize->params = (string)$size[0]['params'];

                $modelProductSize->save(false);

                $ids[] = $modelProductSize->id;
            }
            if (isset($ids) && count($ids)) {
                ProductSize::deleteAll([
                    'AND',
                    'product_id=:product_id',
                    ['not in', 'id', $ids]
                ],
                    [':product_id' => $id]);
            }
        } 
    }
    function _setIsMainStatus($partner){
        // Збільшення ліміту памʼяті, якщо потрібно
        ini_set('memory_limit', '2G');

        // Отримуємо всі товари, що не деактивовані
        $products = \common\models\Product::find()
            ->select(['id','category_id','sku_group','slug', 'price', 'code', 'is_main', 'partner', 'not_available'])
            ->andWhere(["partner" => $partner])
            ->andWhere(['not_active' => 0])
            ->asArray()
            ->all();

        // Побудова масиву $all[partner][sku_group][id] = товар
        $all = [];

        foreach ($products as $product) {
            $partner = $product['partner'];
            if($product['sku_group'] == ''){
                $group = substr(md5($product['category_id'].$product['slug']),0,12);
                $product['sku_group'] = $group;
            }

            $sku = $product['sku_group'];
            $id = $product['id'];
            $all[$partner][$sku][$id] = $product;
        }


        // Логіка вибору головного товару та оновлення
        foreach ($all as $partner => $groups) {
            foreach ($groups as $sku => $products) {
                $mainId = null;

                // Шукаємо товар, який доступний
                foreach ($products as $product) {
                    if ($product['not_available'] == 0) {
                        $mainId = $product['id'];
                        break;
                    }
                }

                // Якщо немає доступних — беремо перший
                if (!$mainId && !empty($products)) {
                    $mainId = reset($products)['id'];
                }

                // Проставляємо is_main
                foreach ($products as $id => $product) {
                    $model = \common\models\Product::findOne($id);
                    if ($model === null) continue;


                    $model->sku_group = $product['sku_group'];
                    $model->is_main = ($id == $mainId) ? 1 : 0;
                    $model->save(false); // без валідації
                }
            }
        }
    }
}
