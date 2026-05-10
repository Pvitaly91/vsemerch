<?php
namespace common\components;

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

class xmlImport{

    protected function createThumbs($path, $thumbFilePath, $thumbs)
    {
        foreach ($thumbs as $profile => $config) {
            $thumbPath = $thumbFilePath[$profile];
            if (is_file($path)) {
                $mimeType = mime_content_type($path);
                if(in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
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

    protected function fileUrlData($url)
    {
        $arr = explode('/', $url);
        $name = $arr[count($arr) - 1];
        $extension = substr(strrchr($name, '.'), 1);
        return [
            'name' => $name,
            'extension' => $extension
        ];
    }
    function import(){
     //   d();
      //  exit;
        $xml_file = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/index.xml");//file_get_contents('http://totobi.com.ua/yml_get/lg3bjy2gvww');
        $xml = new \SimpleXMLElement(trim($xml_file));
        $date = (array)$xml->attributes()->date;
        $date = $date[0];

        $i = 0;
     //   d($xml->shop->categories->category);
     //   exit;
        foreach ($xml->shop->categories->category as $category) {

            $partner_id = (integer)$category['id'];
            $partner_parentId = (integer)$category['parentId'];
            $name = (string)$category;
            if(empty($name)) {
                continue;
            }
            if (!$model = Category::find()->where(['partner' => $this->partner, 'partner_id' => $partner_id])->one()) {
                $model = new Category();
            }
            if (!empty($category['parentId']) && ($modelParent = Category::find()->where(['partner' => $this->partner, 'partner_id' => $partner_parentId])->one())) {
                $model->parent_id = $modelParent->id;
            }
            $model->partner = $this->partner;
            $model->partner_id = $partner_id;
            $model->slug = Inflector::slug($name);
            $model->save(false);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $model->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $model->id;
                $modelTranslate->language = $language;
                $modelTranslate->title = $name;
                $modelTranslate->meta_title = $name;
                $modelTranslate->meta_description = $name;
                $modelTranslate->save(false);
            }
        }
        foreach ($xml->shop->offers->offer as $offer) {
            $partner_id = (integer)$offer['id'];
            $available = (boolean)$offer['available'];
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
                $model = new Product();
            }
            $modelCategory = Category::find()->where(['partner' => $this->partner, 'partner_id' => $category_id])->one();
            $model->partner = $this->partner;
            $model->partner_id = $partner_id;
            $model->category_id = $modelCategory->id;
            $model->not_available = ($available) ? 0 : 1;
            $model->code = $code;
            $model->price_old = $oldprice;
            $model->price = $price;
            $model->slug = Inflector::slug($name);
            if (isset($picture[0])) {
                $fileData = $this->fileUrlData($picture[0]);
                $model->image = $fileData['name'];
            }
            $model->save(false);
            if (isset($picture[0]) && $this->picLoads) {
                $image = Yii::getAlias('@frontend/web/upload/shop/products/' . $model->id . '.' . $fileData['extension']);
                if(!is_file($image)) {
                    copy($picture[0], $image);
                    $thumbFilePath = [
                        'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/thumb_' . $model->id . '.' . $fileData['extension']),
                        'preview' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/preview_' . $model->id . '.' . $fileData['extension']),
                    ];
                    $this->createThumbs($image, $thumbFilePath, $this->thumbs);
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
                        $image = Yii::getAlias('@frontend/web/upload/shop/products/image/'.$modelImage->id. '.' . $fileData['extension']);
                        if(!is_file($image)) {
                            copy($pic, $image);
                            $thumbFilePath = [
                                'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/thumb_' . $modelImage->id . '.' . $fileData['extension']),
                                'ico' => Yii::getAlias('@frontend/web/upload/shop/products/image/thumb/ico_' . $modelImage->id . '.' . $fileData['extension']),
                            ];
                            $this->createThumbs($image, $thumbFilePath, $this->ico);
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
                $modelTranslate->title = $name;
                $modelTranslate->description = $description;
                $modelTranslate->meta_title = $name;
                $modelTranslate->meta_description = $name;
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
                    $modelProductSize->product_id = $model->id;
                    $modelProductSize->size_id = $modelSize->id;
                    $modelProductSize->price = (float)$size[0]['modifier'];
                    $modelProductSize->code = (string)$size[0]['product_code'];
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
            foreach ($offer->param as $param) {
                $option = (string)$param['name'];
                $value = (string)$param[0];
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
            $i++;
            if (($i % 500) == 0) {
                echo $i . '; ';
            }
        }
        echo 'end; ';
    }
}