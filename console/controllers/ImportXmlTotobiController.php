<?php

namespace console\controllers;

use console\models\Category;
use console\models\CategoryTranslate;
use console\models\Image;
use console\models\ImageTranslate;
use common\models\Language;
use console\models\Product;
use console\models\ProductOption;
use console\models\ProductOptionTranslate;
use console\models\ProductSize;
use console\models\ProductTranslate;
use console\models\Size;
use PHPThumb\GD;
use Yii;
use yii\behaviors\SluggableBehavior;
use console\components\Controller;
use yii\helpers\BaseInflector;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

Class ImportXmlTotobiController extends Controller
{
    public $extAvailbleProductsCode = ["1904-01","1901-01","1904-01","1909-08","1901-08"];
    public $partner = 'totobi';
    public $picLoads = true;
    /**
     * Whether existing imported product images should be downloaded again.
     */
    public $overwriteImages = false;
    public $contex = true;
    public $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'overwriteImages',
        ]);
    }
    function get_http_response_code($domain1, $contextOptions = null){
        $contextOptions = $contextOptions ?: $this->arrContextOptions;
        if($this->contex == true)
            $headers = @get_headers($domain1,false,stream_context_create($contextOptions));
        else
            $headers = @get_headers($domain1);

        if ($headers === false || !isset($headers[0])) {
            return '000';
        }

        return substr($headers[0], 9, 3);
    }

    function copyImage($src, $dist){
        try {
            $lastError = null;
            if($this->contex == true)
                $copied = @copy($src, $dist, stream_context_create($this->arrContextOptions));
            else
                $copied = @copy($src, $dist);

            if (!$copied) {
                $lastError = error_get_last();
            }
        } catch (\Throwable $e) {
            $copied = false;
            $lastError = ['message' => $e->getMessage()];
        }

        if (!$copied) {
            $reason = $lastError['message'] ?? 'unknown error';
            $message = "skipped image copy: $src -> $dist; reason: $reason";
            Yii::warning($message);
            echo $message . "\r\n";
            return false;
        }

        return true;
    }

    function setIsMain(){
        
        $products = \common\models\Product::find()->where(["is","sku_group", new \yii\db\Expression('null')])->andWhere(['=',"partner","totobi"])->all();
        $result = [];
        if(count($products) > 0){    
            foreach ($products as $prod){
                $pCode = explode("-",$prod->code);
                $prod->sku_group = $pCode[0];
                $prod->update(false,["sku_group"]);

            }
        }
        $products = \common\models\Product::find()->where(["=", "code",""])->andWhere(["is",'is_main', null])->andWhere(['=',"partner","totobi"])->all();
        
        if(count($products) > 0){
            foreach($products as $prod){
                $prod->is_main = "1";
                $prod->update(false,["is_main"]);
              //  if($prod->validate() == false)            
               //     d($prod->getErrors());
            }
        }
        
        $products = \common\models\Product::find()->where(["NOT",["sku_group" => null]])->andWhere(['=',"partner","totobi"])->all();
        $result = [];
       
        if(count($products) >0){
            foreach ($products as $prod){
               // if(!isset($result[$prod->sku_group]))
                    $result[$prod->sku_group][] = $prod;

            }
           
          //  foreach($result as $prod){
           //     $prod->is_main = 1;
          //      $prod->update(false);
          //  }
        }
     // print_r(count($result)); echo " ";
        foreach($result as $skuGroup => $prods){
            foreach($prods as $prod){
               if($prod->is_main == 1){
                   unset($result[$skuGroup]);
               }
            }   
        }
      //  print_r(count($result));
       //exit;
        foreach($result as $skuGroup => $prods){
            foreach($prods as $prod){
               $prod->is_main = 1;
               $prod->update(false);
               break;
            }   
        }
       
    
    }
    function delNoActiveProds($partnersIDs){
        $products = \common\models\Product::find()->where(["NOT IN","partner_id", $partnersIDs])->andWhere(['=',"partner","totobi"])->all();
        foreach($products as $prod){
            $prod->delete();
          //  echo $prod["partner_id"]."\r\n";
        }
      //  print_r(count($products)); echo "\r\n";
    }
    function actionNormalize(){
     //  exit;
     //   $this->delNoActiveProds(["3556"]);
     //   exit;
        $products = \common\models\Product::find()->where(["is not","sku_group", new \yii\db\Expression('null')])->andWhere(['=',"partner","totobi"])->all();
        $res = [];
        foreach($products as $prod){
            $res[$prod['sku_group']][$prod["slug"]] = [$prod["id"],$prod["code"]];
        }
        foreach($res as $shkGroup => $p){
            if(count($p) < 2){
                unset($res[$shkGroup]);
            }
        }
        print_r($res);  echo "\r\n";
        print_r(count($res));
        
        exit;
    }
    public function actionIsMain(){
        $this->setIsMain();
    }
    public function actionIndex()
    {
   //   import-xml-totobi  
//ALTER TABLE `shop_product` ADD `date_catalog` INT NULL AFTER `partner_id`;
        //ALTER TABLE `shop_product_size` ADD `params` varchar(10) COLLATE 'utf8_general_ci' NULL;
        $unsetOptions = [
                "vaga-asika",
                "kilkist-u-asiku",
                "grupa-koloriv",
                "kilkist-u-asiku",
                "rozdil-u-katalozi",
                "pidrozdil-u-katalozi",
                "dodatkovi-dani",
                "kilkist-v-upakovci",
                "rozmir-asika"
        ];
        
        if($this->contex == true) {

            $xml_file = file_get_contents('http://totobi.com.ua/yml_get/lg3bjy2gvww', false, stream_context_create($this->arrContextOptions));

         //   $xml_file = file_get_contents($_SERVER["DOCUMENT_ROOT"]."E:\OpenServer\domains\agcity.loc\index.xml");
        }else {
            $arrContextOptions = NULL;
            $xml_file = file_get_contents('http://totobi.com.ua/yml_get/lg3bjy2gvww');
        }
       
        $xml = new \SimpleXMLElement($xml_file);
        $i = 0;
        $date = (array)$xml->attributes()->date;
        $date = $date[0];
        $log["date"] = $date;
        $log["start"] = date("d-m-Y H:s:i");
        $date_catalog = strtotime($date);

        foreach ($xml->shop->categories->category as $category) {
            $partner_id = (integer)$category['id'];
            $partner_parentId = (integer)$category['parentId'];
            $name = (string)$category;
            if(empty($name)) {
                continue;
            }
            if (!$model = Category::find()->where(['partner' => $this->partner, 'partner_id' => $partner_id])->one()) {
                $flag = 1;
                $model = new Category();
            }else{
                $flag = 0;
            }
            if (!empty($category['parentId']) && ($modelParent = Category::find()->where(['partner' => $this->partner, 'partner_id' => $partner_parentId])->one())) {
                $model->parent_id = $modelParent->id;
            }
            $model->partner = $this->partner;
            $model->partner_id = $partner_id;
            $model->slug = Inflector::slug($name);

            $model->save(false,null,true);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $model->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $model->id;
                $modelTranslate->language = $language;
                if($flag == 1){
                    $modelTranslate->title = $name;
                    $modelTranslate->meta_title = $name;
                    $modelTranslate->meta_description = $name;
                }
                if(!isset($modelTranslate->seo))
                    $modelTranslate->seo = " ";
                $modelTranslate->save(false);
            }
        }
        $log["count"] = count($xml->shop->offers->offer);
        $partnersIds = [];
        foreach ($xml->shop->offers->offer as $offer) {
            $partner_id = (integer)$offer['id'];
            $available = (array)$offer['available'];
            if(isset($available[0]) && $available[0] == "true"){
                $available = true;
            }elseif(isset($available[0]) && $available[0] == "false"){
                $available = false;
            }else{
                $available = true;
            }

            $category_id = (integer)$offer->categoryId;
            $name = (string)$offer->name;
            if(empty($name)) {
                continue;
            }
           
            $description = (string)$offer->description;
            $code = (string)$offer->vendorCode;
            $oldprice = (float)$offer->oldprice;
            $price = (float)$offer->price;
            if (isset($offer->picture)) {
                if (is_object($offer->picture)) {
                    $picture = (array)$offer->picture;
                } else {
                    $picture[0] = (string)$offer->picture;
                }
            }

            if (!$model = Product::find()->where(['partner' => $this->partner, 'partner_id' => $partner_id])->one()) {
                $pFlag = 1;
                $model = new Product();
            }else{
                $pFlag = 0;
            }
            $modelCategory = Category::find()->where(['partner' => $this->partner, 'partner_id' => $category_id])->one();
			$model->not_active = 0;
            $model->partner = $this->partner;
            $model->partner_id = $partner_id;
            $partnersIds[] = $partner_id;
            if($pFlag == 1){
                $model->category_id = $modelCategory->id;
                $model->not_available = ($available == true) ? 0 : 1;
            }
           
            if($pFlag == 0 && !in_array($code, $this->extAvailbleProductsCode)) 
                $model->not_available = ($available == true) ? 0 : 1;

            $model->code = $code;
            
            $codeParts = explode("-",$code);
            $model->sku_group = $codeParts[0];
          
            $model->price_old = $oldprice;
            $model->price = $price;
            $model->slug = Inflector::slug($name);
            $model->date_catalog = $date_catalog;
            if (isset($picture[0])) {
                $fileData = $this->fileUrlData($picture[0]);
                $model->image = $fileData['name'];
            }

            $model->save(false);
            if (isset($picture[0]) && $this->picLoads ) {

                if(isset($fileData['extension']) && $fileData['extension'] != "" && $this->get_http_response_code($picture[0]) == "200") {
                    $image = Yii::getAlias('@frontend/web/upload/shop/products/' . $model->id . '.' . $fileData['extension']);

                    if ($this->overwriteImages || !is_file($image)) {
                        if ($this->copyImage($picture[0], $image)) {
                            $thumbFilePath = [
                                'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/thumb_' . $model->id . '.' . $fileData['extension']),
                                'preview' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/preview_' . $model->id . '.' . $fileData['extension']),
                            ];
                            $thumbsOptions = is_array($this->thumbs) ? $this->thumbs : [];
                            $this->createThumbs($image, $thumbFilePath, $thumbsOptions);
                        } else {
                            $log["errors_images"][] = "1 ".$picture[0]." -> ".$model->partner_id.' @frontend/web/upload/shop/products/image/'.$model->id. '.' . $fileData['extension'];
                        }
                    }
                }else{
                    $log["errors_images"][] = "1 ".$picture[0]." -> ".$model->partner_id.' @frontend/web/upload/shop/products/image/'.$model->id. '.' . $fileData['extension'];
                }
                if (count($picture) > 1) {
                    $ids = [];
                    foreach ($picture as $key => $pic) {
                        if($key == 0){
                            continue;
                        }
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

                        if(isset($fileData['extension']) && $fileData['extension'] != "" && $this->get_http_response_code($pic,$this->arrContextOptions) == "200"){
                            $image = Yii::getAlias('@frontend/web/upload/shop/products/image/'.$modelImage->id. '.' . $fileData['extension']);
                            if($this->overwriteImages || !is_file($image)) {
                                if (!$this->copyImage($pic, $image)) {
                                    $log["errors_images"][] = "2 ".$pic." -> ".$model->partner_id.' @frontend/web/upload/shop/products/image/'.$modelImage->id. '.' . $fileData['extension'];
                                    continue;
                                }

                                $thumbFilePath = [
                                    'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/thumb_' . $modelImage->id . '.' . $fileData['extension']),
                                    'ico' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/ico_' . $modelImage->id . '.' . $fileData['extension']),
                                ];
                                $this->createThumbs($image, $thumbFilePath, $this->ico);
                            }

                        }else{
                            $log["errors_images"][] = "2 ".$pic." -> ".$model->partner_id.' @frontend/web/upload/shop/products/image/'.$modelImage->id. '.' . $fileData['extension'];
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
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = ProductTranslate::findOne(['shop_product_id' => $model->id, 'language' => $language])) {
                    $modelTranslate = new ProductTranslate();
                }
                $modelTranslate->shop_product_id = $model->id;
                $modelTranslate->language = $language;
                if($pFlag == 1){
                    $modelTranslate->title = $name;
                    $modelTranslate->description = $description;
                    $modelTranslate->meta_title = $name;
                    $modelTranslate->meta_description = $name;
                }
               
                $modelTranslate->save(false);
            }
            $ids = [];
            foreach ($offer->sizes as $sizes) {
                foreach ($sizes->size as $size) {
                    $name = (string)$size[0];
                    if(empty($name)) {
                        continue;
                    }
                   
                    if (!$modelSize = Size::find()->where(['name' => $name])->one()) {
                        $modelSize = new Size();
                    }
                    $modelSize->slug = Inflector::slug($name);
                    $modelSize->name = $name;
                    $modelSize->save(false);

                    if (!$modelProductSize = ProductSize::find()->where(['product_id' => $model->id, 'size_id' => $modelSize->id])->one()) {
                        $modelProductSize = new ProductSize();
                    }
                   
                    if($size[0]['in_stock'] >0 ){
                        $modelProductSize->not_available = 0;
                    }else{
                        $modelProductSize->not_available = 1;
                    }
                    $modelProductSize->product_id = $model->id;
                    $modelProductSize->size_id = $modelSize->id;
                    $modelProductSize->price = (float)$size[0]['modifier'];
                    $modelProductSize->code = (string)$size[0]['product_code'];
                    $modelProductSize->params = (string)$size[0]['params'];
                  
                    $modelProductSize->save(false);
                    $ids[] = $modelProductSize->id;
                }
                if (isset($ids) && count($ids)) {
                    ProductSize::deleteAll([
                        'AND',
                        'product_id=:product_id',
                        ['not in', 'id', $ids]
                    ],
                        [':product_id' => $model->id]);
                }

            }
            $ids = [];
            foreach ($offer->sizes as $sizes) {
                foreach ($sizes->size as $size) {
                    $option = (string)'Розмір';
                    $value = (string)$size[0];
                    if(empty($option) || empty($value)) {
                        continue;
                    }
                    if (!$modelProductOption = ProductOption::find()->where(['product_id' => $model->id, 'slug_option' => Inflector::slug($option), 'slug' => Inflector::slug($value)])->one()) {
                        $modelProductOption = new ProductOption();
                    }
                   
                    $modelProductOption->product_id = $model->id;
                    $modelProductOption->is_filter = true;
                    $modelProductOption->slug_option = Inflector::slug($option);
                    $modelProductOption->slug = Inflector::slug($value);
                    $modelProductOption->partner = "totobi";
                    $modelProductOption->save(false);
                    $ids[] = $modelProductOption->id;
                    foreach (Language::getLanguagesAsArray() as $language) {
                        if (!$modelProductOptionTranslate = ProductOptionTranslate::findOne(['shop_product_option_id' => $modelProductOption->id, 'language' => $language])) {
                            $modelProductOptionTranslate = new ProductOptionTranslate();
                        }
                        $modelProductOptionTranslate->shop_product_option_id = $modelProductOption->id;
                        $modelProductOptionTranslate->language = $language;
                        $modelProductOptionTranslate->option = $option;
                        $modelProductOptionTranslate->value = $value;
                        $modelProductOptionTranslate->save(false);
                    }
                }
                if (isset($ids) && count($ids)) {
                    ProductOption::deleteAll([
                        'AND',
                        'product_id=:product_id',
                        ['not in', 'id', $ids]
                    ],
                        [':product_id' => $model->id]
                    );
                }

            }
            $ids = [];
            $attr = [];

            foreach ($offer->param as $k => $param) {
                $option = (string)$param['name'];
                $value = (string)$param[0];
                if(empty($option) || empty($value)) {
                    continue;
                }
                if( in_array( Inflector::slug($option),$unsetOptions)){
                    continue;
                }
             //   print_r("option: ".$option."\r\n");
             //   print_r("value: ".$value."\r\n");
                $this->setProps($option, $value, $ids, $model); 
            }
            \common\Helpers\Partners::availableProp($model->not_available, $attr);
            $option = key($attr["uk"]);
            $this->setProps($option, $attr["uk"][$option], $ids, $model);

            if (isset($ids) && count($ids)) {
                ProductOption::deleteAll([
                    'AND',
                    'product_id=:product_id',
                    ['not in', 'id', $ids]
                ],
                    [':product_id' => $model->id]
                );
            }
            $i++;

            if (($i % 500) == 0) {
                echo $i . '; ';

            }
        }
        $this->delNoActiveProds($partnersIds);
        $this->setIsMain();    
        echo 'end; '."\n";
        $log["end"] = date("d-m-Y H:s:i");
        file_put_contents(Yii::$app->runtimePath."\logs\log-".time().".txt",var_export($log,true));
    }
 
    function setProps($option,$value,&$ids,&$model){
        
        if (!$modelProductOption = ProductOption::find()->where(['product_id' => $model->id, 'slug_option' => Inflector::slug($option), 'slug' => Inflector::slug($value)])->one()) {
            $modelProductOption = new ProductOption();
        }
        $modelProductOption->product_id = $model->id;
        $modelProductOption->is_filter = true;
        $modelProductOption->slug_option = Inflector::slug($option);
        $modelProductOption->slug = Inflector::slug($value);
        $modelProductOption->save(false);
        $ids[] = $modelProductOption->id;
        foreach (Language::getLanguagesAsArray() as $language) {
            if (!$modelProductOptionTranslate = ProductOptionTranslate::findOne(['shop_product_option_id' => $modelProductOption->id, 'language' => $language])) {
                $modelProductOptionTranslate = new ProductOptionTranslate();
            }
            $modelProductOptionTranslate->shop_product_option_id = $modelProductOption->id;
            $modelProductOptionTranslate->language = $language;
            $modelProductOptionTranslate->option = $option;
            $modelProductOptionTranslate->value = $value;
            $modelProductOptionTranslate->save(false);
        }
    }

}
