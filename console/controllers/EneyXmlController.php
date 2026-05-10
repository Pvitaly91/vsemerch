<?php

namespace console\controllers;

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
use common\models\ProductMap;
class EneyXmlController extends Controller {
    /*
     *  Eney
     */
    /* console Controller */

    protected function fileUrlData($url) {
        $arr = explode('/', $url);
        $name = $arr[count($arr) - 1];
        $extension = substr(strrchr($name, '.'), 1);
        return [
            'name' => $name,
            'extension' => $extension
        ];
    }

    public $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    protected $thumbs = [
        'thumb' => ['width' => 300, 'height' => 300],
        'preview' => ['width' => 400, 'height' => 400],
    ];
    protected $ico = [
        'ico' => ['width' => 100, 'height' => 100],
        'thumb' => ['width' => 400, 'height' => 400],
    ];
    public $sizeTeble = [
        "S",
        "M",
        "L",
        "XL",
        "2XL",
        "3XL",
        "4XL",
        "5XL",
        "6XL",
        "XXL",
        "2XXL",
        "3XXL",
        "4XXL",
        "5XXL",
        "6XXL"
    ];
    public $optionMap = [];
    public $test = [];
    function getOptionMap($value){
      
       // return $value; 
        return (isset($this->optionMap[$value]))? $this->optionMap[$value] : $value;
        
    }
    
    function initMap(){
        $models = \common\models\OptionMap::find()->where(["=","partner","eney"])->all();
        foreach ($models as $item){
            $this->optionMap[$item->partner_slug] = $item->site_slug;
        }
        
    }
    function makeCategoryArray() {

        $xml = simplexml_load_file("https://www.eney.com.ua/system/exchanger/more_products_options_dop_category.xml");

        // Initialize an array to store the parsed products
        $products = [];

        // Iterate over each product element

        foreach ($xml->product as $product) {
            /*
              $catFirst = $product->categories->category[0];

              $fcat = Inflector::slug(trim((string) $catFirst->category_ua));
              $fcatId = (string)$product['option_id'];
              $products[$fcat] = [
              "lng" =>[
              'uk' => trim((string) $catFirst->category_ua),
              'ru' => trim((string) $catFirst->category_ru),
              'en' => trim((string) $catFirst->category_en)
              ],
              "id" => $fcatId,
              // "parent_id" => $mainCat_id
              ];
             */
            foreach ($product->categories->category as $category) {
                $catName = Inflector::slug(trim((string) $category->category_ua));

                $categoryUa = (string) $category->category_ua;
                $categoryRu = (string) $category->category_ru;
              //  $categoryEn = (string) $category->category_en;
                $items = [
                    "lng" => [
                        'uk' => $categoryUa,
                        'ru' => $categoryRu,
                  //      'en' => $categoryEn
                    ],
                        //  "parent_id" => $fcatId
                ];
                if (isset($product['option_id']))
                    $items["id"] = (string) $product['option_id'];
                $products[$catName] = $items;

                $items = [];
            }
        }
        return $products;
    }
    
    function makeProductsCategories() {

        $xml = simplexml_load_file("https://www.eney.com.ua/system/exchanger/more_products_options_dop_category.xml");

        // Initialize an array to store the parsed products
        $products = [];

        // Iterate over each product element
        foreach ($xml->product as $product) {
            foreach ($product->categories->category as $category) {
                $products[(int) $product['option_id']] = Inflector::slug(trim((string) $category->category_ua));
            }
        }
        $cats = [];
        foreach ($products as $id => $slug) {
            /*if(($map = $this->getCategoryMapArray($slug))== true){
                print_r($slug);
                print_r($map);
                $slug = $map["slug"];
                print_r($slug);
                exit;
            }*/
            $cats[$slug][] = $id;
        }
        //  dd(count($products));
        //dd($cats);
        //  dd($products);
        return $cats;
    }

    function createEneyCat() {
       
        if ($this->getEneyCatId("eney") == null) {
            $model = new Category();
            $model->slug = $model->partner_slug = $model->partner = "eney";
            $model->active = "1";
            $model->save(false);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $model->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $model->id;
                $modelTranslate->language = $language;
                $modelTranslate->seo = $modelTranslate->meta_description = $modelTranslate->meta_title = $modelTranslate->title = "Eney";

                $modelTranslate->save(false);
            }
        }
    }

    function getCatModel($slug) {
        if (($model = Category::find()->where(['slug' => $slug, 'partner' => "eney"])->one()) == true) {
            return $model;
        }
    }

    function getEneyCatId($slug) {
        if (($model = $this->getCatModel($slug)) == true) {
            return $model->id;
        }
    }
    function getCatId($slug){
        if (($model = Category::find()->where(['slug' => $slug])->one()) == true) {
            return $model->id;
        }
    }
    function delEneyCat($slug) {
        if (($model = Category::find()->where(['slug' => $slug, 'partner' => "eney"])->one()) == true) {
            $model->delete();
        }
    }

    function dellAllEneyCats() {
        if (($models = Category::find()->where(['partner' => "eney"])->all()) == true) {
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }

    function getEneyProductsArray() {
        $xmlFile = 'https://www.eney.com.ua/system/exchanger/ext/more_products_options.xml';
        $xml = simplexml_load_file($xmlFile);

        // Initialize an array to store the parsed products
        $products = $colorBase = [];
        $category_id = $this->getEneyCatId("eney");
      
        // Iterate over each product element
        foreach ($xml->product as $product) {
            // Parse the product attributes
            $partner_id = (string) $product['option_id'];
            $code = (string) $product['model'];
            $quantity = (int) $product->quantity;
            $not_available = ($quantity < 1) ? 1 : 0;

            $productId = (int) $product->product_id;
            $price = (float) $product->price;
            $reserve = (int) $product->reserve;

            $image = (string) $product->image;

            // Parse the names in different languages
            $nameRu = (string) str_replace("ENEY", "", $product->name_ru);
            $nameUa = (string) str_replace("ENEY", "", $product->name_ua);
           
          
            // Parse the option names in different languages
            //  $optionNameRu = (string) $product->option_name_ru;
             $optionNameUa = (string) $product->option_name_ua;
            // Parse the attributes
            $attributes = [];
            foreach ($product->attributes->children() as $attribute) {
                $attributeName = $attribute->getName();
                $attributeValue = (string) $attribute;
              
                $attributes["ru"][$attributeName] = $attributeValue;
            }
            if(isset($attributes["ru"]) && is_array($attributes["ru"])){
                foreach($attributes["ru"] as $attrName => $attrValue){
                   $_attrName = substr($attrName, -3);
                   if($_attrName == "_ua"){
                       unset($attributes["ru"][$attrName]);
                       $option = str_replace($_attrName, "", $attrName);
                       $attributes["uk"][$option] = $attrValue;
               //        $attributes["en"][$option] = $attrValue;
                   }
               }
            }
            
            if(strpos($nameUa, "жіноче") !== false || strpos($nameUa, "жіноч") !== false)
            {
                $genderCode= "Стать";  
                $attributes["uk"][$genderCode] = $attributes["ru"][$genderCode] = "для жінок";
            }elseif(strpos($nameUa, "чоло") !== false){
                $genderCode= "Стать";
                $attributes["uk"][$genderCode] = $attributes["ru"][$genderCode]  = "для чоловіків";
            }elseif( strpos($nameUa, "унісекс") !== false ){
                $genderCode= "Стать";
                $attributes["uk"][$genderCode] = $attributes["ru"][$genderCode]  = "унісекс";
            }
            $optionName = "color";  
         
           // if(!IS_PROD){
           \common\Helpers\Partners::availableProp($not_available, $attributes);// set available status by as property
           // }
                
            if(in_array($optionNameUa, $this->sizeTeble)){
              //  $optionName = "_size"; 
               $sku_group = (string)substr($code,0,6);
              //   if($optionNameUa != NULL)
               $partsName = explode(",",$nameUa);
                if(count($partsName) >1){
                    if(isset($attributes["uk"]["model"]) && trim(end($partsName)) == trim($attributes["uk"]["model"])){
                        unset($partsName[key($partsName)]);
                        reset($partsName);
                    }
                    $col = end($partsName);
                    $colorBase[$col] = $nameUa;
                    
                    $attributes["uk"][$optionName] = $attributes["ru"][$optionName] = $col;
                    unset($partsName[key($partsName)]);
                    $nameUa = implode(",",$partsName);
                   // $attributes["uk"]["productId"] = $attributes["ru"]["productId"] = $attributes["en"]["productId"]  = $productId;
            
                }
                    
            }else{
                $sku_group = (string)$productId;
                if($optionNameUa != NULL)
                    $attributes["uk"][$optionName] = $attributes["ru"][$optionName] = $optionNameUa;
            
            }
            
           
       
            $productData = [
                'partner_id' => $partner_id,
                'code' => $code,
                'sku_group' => $sku_group,
                'price' => $price,
                // 'reserve' => $reserve,
                'quantity' => $quantity,
                'not_available' => $not_available,
                'category_id' => $category_id,
                'image' => $image,
                'slug' => Inflector::slug($nameUa),
                'is_main' => 1,
                'title_ru' => $nameRu,
                'title' => $nameUa,
                'partner' => "eney",
                'productId' => $productId
            ];
            $translateData = [
                'title_ru' => $nameRu,
                'title_uk' => $nameUa,
            ];
            // Create a product array with the parsed data
            $parsedProduct = [
                "productData" => $productData,
                "translateData" => $translateData,
                'attributes' => $attributes,
            ];
         
          //  if($optionNameUa == "2XL"){
         //       print_r($parsedProduct);
          //      exit;
         //   }
            // Add the parsed product to the products array
            $products[] = $parsedProduct;
            
        }
        //set is main
        $_sizes = [];
        $ids = [];
        foreach($products as $k => &$prod){
            
            if(isset($prod["attributes"]["uk"]["size_clothes"])){
               
                $id = $prod["productData"]["productId"];
               
                if(!in_array($id, $ids) ){

                    $ids[] = $id;         
                }else{
                    unset($products[$k]);
                }
            $_sizes[$id][] =[ 
                    "name" => $prod["attributes"]["uk"]["size_clothes"],
                    "not_available" => $prod["productData"]['not_available'],
                    "price" => $prod["productData"]['price'],
                    "code" => $prod["productData"]['code'],
                    "partner_id" => $prod["productData"]['partner_id'],
                ];
            }
        }
      
        $ids = [];
        foreach($products as &$prod){
            $id = $prod["productData"]["sku_group"];
            if(!in_array($id, $ids)){
                $prod["productData"]["is_main"] = 1;
                $ids[] = $id;
            }else{
                $prod["productData"]["is_main"] = 0; 
            }
            if(isset($_sizes[$prod["productData"]["productId"]])){
                $prod["sizes"] = $_sizes[$prod["productData"]["productId"]];
                // print_r($prod);
            }
           
        }
       
        return $products;
    }
    function getEneyProductsModels() {
        $products = Product::find()->where(['partner' => "eney"])->all();
        return $products;
    }

    function delAll() {
        foreach ($this->getEneyProductsModels() as $model) {
            $model->delete();
        }
    }

    function getProductModel($partner_id,&$flag =false) {
        if (!$model = Product::find()->where(['partner' => "eney", 'partner_id' => $partner_id])->one()) {
            $model = new Product();
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $model;
    }
    public $savedProducts = [];
    function setProduct($data) {
        $flag = 0;
        $model = $this->getProductModel($data['partner_id'],$flag);
        $model->partner_id = $data["partner_id"];
        $model->code = $data["code"];
       // $model->sku_group = $data["sku_group"];
        $model->price = $data["price"];
        $model->not_available = $data["not_available"];
        if($flag == 1){
            $model->category_id = $data["category_id"];
            $model->sku_group = $data["sku_group"];
        }    
        if($model->not_active == NULL){
            $model->not_active = 0;
        }
        $model->image = $data["image"];
        $model->slug = $data["slug"];
        $model->is_main = $data["is_main"];
        $model->partner = $data["partner"];
     //  print_r($data);
       // exit;
       // $model->setAttributes($data);
       
        if ($model->save(false)) {
            $this->savedProducts[] = $model->id;
            return $model->id;
        }else{
            print_r($data);
             print_r($model->getErrors());
             exit;
        }
    //    print_r($model);
     //   exit;
    }


    public function eneyInsertProducts() {
        $this->initMap();
        foreach ($this->getEneyProductsArray() as $k => $productData) {
            /****************products*********************/
          
            if (($id = $this->setProduct($productData["productData"])) == true) {
                $translateData = $productData["translateData"];
                foreach (Language::getLanguagesAsArray() as $language) {
                    if (!$modelTranslate = ProductTranslate::findOne(['shop_product_id' => $id, 'language' => $language])) {
                        $pFlag = 1;
                        $modelTranslate = new ProductTranslate();
                    }else{
                        $pFlag = 0;
                    }
                    if (isset($translateData["title_" . $language])) {
                        $modelTranslate->shop_product_id = $id;
                        $modelTranslate->language = $language;
                        if($pFlag == 1){
                            $modelTranslate->title = $translateData["title_" . $language];
                            $modelTranslate->description = $modelTranslate->title;
                            $modelTranslate->meta_title = $modelTranslate->title;
                            $modelTranslate->meta_description = $modelTranslate->title;
                        }
                        
                        $modelTranslate->save(false);
                    }
                }
            }
            /****************attributes*********************/
              $ext = \common\Helpers\Partners::$ext["eney"];
            if (isset($productData["attributes"]["uk"]) && is_array($productData["attributes"]["uk"])) {
                foreach ($productData["attributes"]["uk"] as $option => $value) {
                    if(in_array($option, $ext)){
                        continue;
                    }
                    
                    if (!$modelProductOption = ProductOption::find()->where(['product_id' => $id, 'slug_option' => Inflector::slug($option), 'slug' => Inflector::slug($value)])->one()) {
                        $modelProductOption = new ProductOption();
                    }
               
                    $modelProductOption->product_id = $id;
                    $modelProductOption->is_filter = true;
                    $modelProductOption->slug_option = $this->getOptionMap(Inflector::slug($option));
                    $modelProductOption->slug = Inflector::slug($value);
                    $modelProductOption->partner = "eney";
                    $modelProductOption->save(false);
                    $ids[] = $modelProductOption->id;
                    foreach (Language::getLanguagesAsArray() as $language) {
                        if (!$modelProductOptionTranslate = ProductOptionTranslate::findOne(['shop_product_option_id' => $modelProductOption->id, 'language' => $language])) {
                            $modelProductOptionTranslate = new ProductOptionTranslate();
                        }
                        $modelProductOptionTranslate->shop_product_option_id = $modelProductOption->id;
                        $modelProductOptionTranslate->language = $language;
                        $modelProductOptionTranslate->option = $option;
						if(isset($productData["attributes"][$language][$option])){
							$modelProductOptionTranslate->value = str_replace("м²", "м2", $productData["attributes"][$language][$option]);
						}
                        
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
            if (isset($productData["sizes"])) {
               
                foreach($productData["sizes"] as $size){
                  //  print_r($size);
                  //  print_r(date("d-m-Y h:s:i"));
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
      //  printr_r($this->savedProducts);
        $products = Product::find()->where(["not in","id",$this->savedProducts])->andWhere(["partner" => "eney"])->all();
      
       
        
        if(is_array($products)){
            foreach($products as $prod){
                $prod->not_available = 1;
            //    echo $prod->code."\r\n";
                $prod->save(false);
            }
        }
        /*
34N708DF2
34N7095F2
51K036C00
35N711D10
34N714D04
50S0810077
51K070M66
51K070M00         */
      //  exit;
    }

    protected function createThumbs($path, $thumbFilePath, $thumbs) {
        foreach ($thumbs as $profile => $config) {
            $thumbPath = $thumbFilePath[$profile];
            //    dd($thumbPath);
            if (is_file($path)) {
                $mimeType = mime_content_type($path);
                // dd($config);
                if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                    //  dd([$path, $config]);
                    $thumb = new GD($path, $config);

                    $processor = function (GD $thumb) use ($config) {
                        $thumb->adaptiveResize($config['width'], $config['height']);
                    };
                    call_user_func($processor, $thumb, $path);
                    FileHelper::createDirectory(pathinfo($thumbPath, PATHINFO_DIRNAME), 0775, true);
                    $thumb->save($thumbPath);
                }
            }
        }
    }

    /* console Controller */

    function get_http_response_code($domain1) {

        $headers = get_headers($domain1, false, stream_context_create($this->arrContextOptions));

        return substr($headers[0], 9, 3);
    }

    function recordPhoto($photoUrl, $productId) {
        $fileData = $this->fileUrlData($photoUrl);
        if (isset($fileData['extension']) && $fileData['extension'] != "" && $this->get_http_response_code($photoUrl) == "200") {
            $image = Yii::getAlias('@frontend/web/upload/shop/products/' . $productId . '.' . $fileData['extension']);

         //   if (!is_file($image)) {

                copy($photoUrl, $image, stream_context_create($this->arrContextOptions));

                $thumbFilePath = [
                    'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/thumb_' . $productId . '.' . $fileData['extension']),
                    'preview' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/preview_' . $productId . '.' . $fileData['extension']),
                ];
                $this->createThumbs($image, $thumbFilePath, $this->thumbs);
         //   }
        }
    }

    function eneyMakePhotos() {

        foreach ($this->getEneyProductsModels() as $model) {

            $this->recordPhoto($model->image, $model->id);
        }
    }

    function setCategory($slug, $data, $parent_id = false) {
        if (!$model = $this->getCatModel($slug)) {
            $cFlag = 1;
            $model = new Category();
        }
        else{
            $cFlag =0;
        }
        if ($parent_id != false) {
            $model->parent_id = $parent_id;
        }
        if (isset($data["id"])) {
            $model->partner_id = $data["id"];
        }
        $model->slug = $model->partner_slug = $slug;
        $model->partner = "eney";
        $model->active = "1";
        $model->save(false);
        foreach (Language::getLanguagesAsArray() as $language) {
            $title = $data["lng"][$language];
            if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $model->id, 'language' => $language])) {
                $modelTranslate = new CategoryTranslate();
            }
            $modelTranslate->shop_category_id = $model->id;
            $modelTranslate->language = $language;
            if($cFlag == 1)
                $modelTranslate->meta_description = $modelTranslate->meta_title = $modelTranslate->title = str_replace("ENEY", "", $title);

            if(!isset($modelTranslate->seo))
                $modelTranslate->seo = " "; 

            $modelTranslate->save(false);
        }
        return $model->id;
    }
    function getEneyCatMap(){
        $maps=  CategoriesMap::find()->where(['=','partner' , 'eney'])->all();
        $coincidence = [];
        
       foreach($maps as $cat){
           $coincidence[$cat->partner_slug] = array (
                'slug' => $cat->site_slug,
                'type' => $cat->type,
              );
           
       }
       return $coincidence;
    }
    function getCategoryMapArray($neySlug){
        $coincidence = $this->getEneyCatMap();
        if(isset($coincidence[$neySlug])){
            return$coincidence[$neySlug];
        }
            
        
    }
    function getSiteCategory($slug){
        $coincidence = $this->getEneyCatMap();
     
        if(isset($coincidence[$slug]) && ($cat = $coincidence[$slug]) == true){
 
            if($coincidence[$slug]["type"] == "subcat"){
                if (($model = Category::find()->where(['slug' => $coincidence[$slug]["slug"]])->andWhere(["!=",'partner',"eney"])->one()) == true) {
                    return $model->id;
                }else{
                    
                    return $this->getEneyCatId("eney");
                }
            }elseif($coincidence[$slug]["type"] == "merge" && $coincidence[$slug]["slug"] != $slug){
               if (($model = Category::find()->where(['slug' => $coincidence[$slug]["slug"]])->andWhere(["!=",'partner',"eney"])->one()) == true) {
                    return $this->getEneyCatId("eney");
                }
                return false;
            }
            
           
        }
        else{
           
           //  print_r($slug); echo" \r\n" ;
             
             return $this->getEneyCatId("eney");
        }
    }
    function insertCategories() {
        
        
      
        if (($cats = $this->makeCategoryArray()) == true && is_array($cats)) {
            //print_r($cats);
          //  exit;
            foreach ($cats as $slug => $data) {
                if(($catId = $this->getSiteCategory($slug)) == true)
                    $this->setCategory($slug, $data,$catId);
                else{
                  //  print_r($slug); echo "\r\n" ;
                }
            }
        }
    }

    function attachCats() {
        foreach ($this->makeProductsCategories() as $slug => $ids) {
            $catMap = CategoriesMap::find()->where(['=','partner' , 'eney'])->andWhere(['=',"partner_slug",$slug])->one();
            if($catMap != NULL && $catMap->type == "merge")
                $slug = $catMap->site_slug;
                
                $catId = $this->getCatId($slug);
        
            if (is_array($ids)) {
               foreach ($ids as $productId) {
                    $flag = 0;
                    $model = $this->getProductModel($productId,$flag);
                    if($flag == 1){
                        if(($producMapModel = ProductMap::find()->where(['partner_product_id' => $model->partner_id, 'product_partner' => "eney"])->one())== true){
                            $category = Category::find()->where(["partner_id" => $producMapModel->category_partner_id,"partner" => $producMapModel->category_partner])->one();
                            $catId = $category->id;
                        }

    //   $category = Category::find()->where(["partner_product_id" => $model->id,"product_partner" => "eney" ])->one();
                      //  $category = $category->id;

                        $model->category_id = $catId;
                        $model->save(false);
                    }
                }
            }
        }
    }
    function checkImg($img){
        $img = str_replace("eney.com.ua", "www.eney.com.ua", $img);
        
        if(($model = Product::find()->where(['partner' => "eney", 'image' => $img])->one()) == true){
            return true;
        }else{
            return false;
        }
    }
    function parseImages(){
                // Load the XML file
        $xmlFile = 'https://www.eney.com.ua/system/exchanger/more_products_options_dop_image.xml';
        $xml = simplexml_load_file($xmlFile);

        // Initialize an array to store the parsed products
        $products = [];

        // Iterate over each product element
        foreach ($xml->product as $product) {
            // Parse the product attributes
            $optionId = (string)$product['option_id'];
            $model = (string)$product['model'];
            $productId = (int)$product->product_id;

            // Parse the images
            $images = [];
            foreach ($product->images->image as $image) {
                $imageUrl = (string)$image;
                $img = str_replace("http:", "https:", $imageUrl);
                if($this->checkImg($img) == false)
                    $images[] = $img;
               
            }

            // Create a product array with the parsed data
            $parsedProduct = [
                'option_id' => $optionId,
                'model' => $model,
                'product_id' => $productId,
                'images' => $images,
            ];

            // Add the parsed product to the products array
            $products[] = $parsedProduct;
        }
       
        return $products;
    }
    function setMorePhotos($pictures,$model){
        $ids = [];
        if(is_array($pictures) && !empty($pictures)){
            foreach ($pictures as $key => $pic) {

                $fileData = $this->fileUrlData($pic);
                if (!$modelImage = Image::findOne(['product_id' => $model->id, 'image' => $fileData['name']])) {
                    $modelImage = new Image();
                }
                $modelImage->product_id = $model->id;
                $modelImage->image = $fileData['name'];
                $modelImage->save(false);

                $ids[] = $modelImage->id;
                foreach (Language::getLanguagesAsArray() as $language) {
                    if (!$modelImageTranslate = ImageTranslate::findOne(['shop_product_image_id' => $modelImage->id, 'language' => $language])) {
                        $modelImageTranslate = new ImageTranslate();
                    }
                    $modelImageTranslate->shop_product_image_id = $modelImage->id;
                    $modelImageTranslate->language = $language;
                    $modelImageTranslate->alt = '';
                    $modelImageTranslate->save(false);
                }

                if (isset($fileData['extension']) && $fileData['extension'] != "" && ($this->get_http_response_code($pic, $this->arrContextOptions) == "301" || $this->get_http_response_code($pic, $this->arrContextOptions) == "200")) {
                    $image = Yii::getAlias('@frontend/web/upload/shop/products/image/' . $modelImage->id . '.' . $fileData['extension']);

               //     if (!is_file($image)) {

                        copy($pic, $image, stream_context_create($this->arrContextOptions));

                        $thumbFilePath = [
                            'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/thumb_' . $modelImage->id . '.' . $fileData['extension']),
                            'ico' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/ico_' . $modelImage->id . '.' . $fileData['extension']),
                        ];
                        $this->createThumbs($image, $thumbFilePath, $this->ico);
                   // }
                } 
            }
            if (isset($ids) && count($ids)) {
                Image::deleteAll([
                    'AND',
                    'product_id=:product_id',
                    ['not in', 'id', $ids]
                        ],
                        [':product_id' => $model->id]);
            }
        }    
    }
    function recordMorePhotos(){
        foreach($this->parseImages() as $images){
        
            if(($model = Product::find()->where(['partner' => "eney", 'partner_id' => $images["option_id"]])->one()) == true){
                $this->setMorePhotos($images["images"], $model);
      
            }
        }
    }
    function deleteEmptyCats(){
        $models = Category::find()
                ->where(['partner' => "eney"])
                ->andWhere(["not",["parent_id" => null]])
                ->all();
        
        if(is_array($models) && !empty($models)){
            foreach($models as $model){
                if(count(Product::find()
                        ->where(['partner' => "eney", 'category_id' => $model->id])
                        ->all()) == 0)
                {
                    $model->active = 0;
                    $model->save(false);
                   // echo $model->id."\r\n";
                }
            }
        }
    }
    function actionTest() {
     //  $this->optionMaping();
    }
    function optionMaping(){
        $optionMapping = \common\models\OptionMap::find()->all();
        $catsMap = [];
        $partner = "eney";
        foreach($optionMapping as $map){
            $catsMap[$map["category_id"]][] = [
                "site_slug" => $map->site_slug,
                "partner_slug" => $map->partner_slug,
            ];
        }
        foreach($catsMap as $catid => $map){
            $products_partners = Product::find()->where(["=","category_id",$catid])->andWhere(["=","partner",$partner])->all();     
           
            
            foreach($map as $item){
                foreach ($products_partners as $pModel) {
                    foreach ($pModel->option as $option) {

                        if ($option->slug_option == $item["partner_slug"]) {

                            $optionModel = \console\models\ProductOption::findOne($option->id);

                            $optionModel->slug_option = $item["site_slug"];

                            $optionModel->save(false);
                            // dd($optionModel);
                            break;
                        }
                    }
                }
            }
        }
    
    }
    function clearDb(){
        exit;
        $cats = [1,2,3,4];
        foreach($cats as $catId){
            
            $all = Product::find()->where(["=","category_id",$catId])->all();
            if(is_array($all)){
                foreach($all as $prod ){
                    $prod->not_active = 1;
                    $prod->update(false);
                 
                }
            }    
            $all = \common\models\Category::find()->where(["=","id",$catId])->all();
            if(is_array($all)){
                foreach($all as $prod ){
                    $prod->active = 0;
                    $prod->update(false);
                } 
            }
           
        }
        
        exit;
        $all = Product::find()->where(["=","partner","bergamo"])->all();
     
       foreach($all as $prod ){
           $prod->delete();
       }
        $all = \common\models\Category::find()->where(["=","partner","bergamo"])->all();
         foreach($all as $prod ){
           $prod->delete();
       }
        $all = \common\models\ProductOption::find()->where(["is",'product_id', null])->all();
       
         foreach($all as $prod ){
           $prod->delete();
       }
        exit;
    }
    
    function actionDelete() {
        $this->delAll();
        $this->dellAllEneyCats();
    }

    function actionCats() {
        $this->createEneyCat();
        $this->insertCategories(); 
        
    }

    function actionAttachCats() {
        $this->attachCats();
        $this->deleteEmptyCats();
    }

    function actionIndex() {
       
     //$this->clearDb();
     // exit;
        $this->createEneyCat();
        $this->eneyInsertProducts();
      
    }
    function actionClearDb(){
        $this->clearDb();
    }
    function actionPhotos() {
        $this->eneyMakePhotos();
        $this->recordMorePhotos();
    }
    function actionUpdateNoPic(){
        echo "start ".date("d-m-Y H:i:s")." \r\n";
        $this->insertCategories();
        echo date("d-m-Y H:i:s")." create cats \r\n";
        $this->eneyInsertProducts();
        echo date("d-m-Y H:i:s")." record products \r\n";
        $this->attachCats();
        echo date("d-m-Y H:i:s")." attach cats \r\n";
        $this->deleteEmptyCats();
        echo date("d-m-Y H:i:s")." deactive empty cats \r\n";
        $this->optionMaping();
        echo date("d-m-Y H:i:s")." option mapping \r\n";
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }
    function actionUpdate(){
        echo "start ".date("d-m-Y H:i:s")." \r\n";
        $this->insertCategories();
        echo date("d-m-Y H:i:s")." create cats \r\n";
        $this->eneyInsertProducts();
        echo date("d-m-Y H:i:s")." record products \r\n";
        $this->attachCats();
        echo date("d-m-Y H:i:s")." attach cats \r\n";
        $this->deleteEmptyCats();
        echo date("d-m-Y H:i:s")." deactive empty cats \r\n";
        $this->optionMaping();
        echo date("d-m-Y H:i:s")." option mapping \r\n";
        $this->eneyMakePhotos();
        echo date("d-m-Y H:i:s")." attach photos \r\n";
        $this->recordMorePhotos();
        echo date("d-m-Y H:i:s")." attach more photos \r\n";
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }
    function actionRemakeAll() {
        echo "start ".date("d-m-Y H:i:s")." \r\n";
        $this->delAll();
        echo date("d-m-Y H:i:s")." del all products \r\n";
        $this->dellAllEneyCats();
        echo date("d-m-Y H:i:s")." del all cats \r\n";
        // $this->delEneyCat("eney");

        $this->createEneyCat();
        $this->insertCategories();
        echo date("d-m-Y H:i:s")." create cats \r\n";
        $this->eneyInsertProducts();
        echo date("d-m-Y H:i:s")." record products \r\n";
        $this->attachCats();
        echo date("d-m-Y H:i:s")." attach cats \r\n";
        $this->deleteEmptyCats();
        echo date("d-m-Y H:i:s")." deactive empty cats \r\n";
        $this->optionMaping();
        echo date("d-m-Y H:i:s")." option mapping \r\n";
        $this->eneyMakePhotos();
        echo date("d-m-Y H:i:s")." attach photos \r\n";
        $this->recordMorePhotos();
        echo date("d-m-Y H:i:s")." attach more photos \r\n";
        echo "end ".date("d-m-Y H:i:s")." \r\n";
    }

}
