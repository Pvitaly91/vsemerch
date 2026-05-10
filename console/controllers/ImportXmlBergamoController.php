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

Class ImportXmlBergamoController extends Controller
{
    public $partner = 'bergamo';
    public $picLoads = true;

    public function actionIndex()
    {
        exit;
        $xml_file = file_get_contents('ftp://stock:Stua9000@ftp.bergamo.ua/items.xml');
        $xml = new \SimpleXMLElement($xml_file);
        $productIds = [];
        $i = 0;
        foreach ($xml->Item as $item) {
            $Artikul = (string)$item->Artikul;
            $Colornumber = (string)$item->Colornumber;
            $siteCode = str_replace($Colornumber, '', $Artikul);
            $Group = (string)$item->Group;
            if (empty($Group)) {
                continue;
            }
            $Ostatok = (integer)$item->Ostatok;
            $GroupSlug = Inflector::slug($Group);
            $picture = (string)$item->Picture1;
            $pictures = [];
            $pictures[] = (string)$item->Picture2;
            $pictures[] = (string)$item->Picture3;
            $pictures[] = (string)$item->Picture4;
            $pictures[] = (string)$item->Picture5;
            $pictures[] = (string)$item->Picture6;
            $pictures[] = (string)$item->Picture7;
            $pictures[] = (string)$item->Picture8;
            $pictures[] = (string)$item->Picture9;
            $pictures[] = (string)$item->Picture10;
            $pictures[] = (string)$item->Picture11;
            $pictures[] = (string)$item->Picture12;

            $options = [];
            $Color = (string)$item->Color;
            if (!empty($Color)) {
                $options['Цвет'] = strstr($Color, '_', true);
            }
            $Proizvoditel = (string)$item->Proizvoditel;
            if (!empty($Proizvoditel)) {
                //$options['Производитель'] = $Proizvoditel;
            }
            $Brand = (string)$item->Brand;
            if (!empty($Brand)) {
                //$options['Бренд'] = $Brand;
            }
            $GrouppaNaneseniya = (string)$item->GrouppaNaneseniya;
            if (!empty($GrouppaNaneseniya)) {
                $options['Нанесения'] = $GrouppaNaneseniya;
            }
            $Indupakovka = (string)$item->Indupakovka;
            if (!empty($Indupakovka)) {
                $options['Упаковка'] = $Indupakovka;
            }

            if($Brand == 'Bergamo') {
                continue;
            }

            $parentCategory = 'Товары по брендам';
            if (!$modelCategoryParent = Category::find()->where(['partner_slug' => Inflector::slug($parentCategory), 'partner' => $this->partner])->one()) {
                $modelCategoryParent = new Category();
            }
            $modelCategoryParent->partner = $this->partner;
            $modelCategoryParent->partner_slug = Inflector::slug($parentCategory);
            $modelCategoryParent->slug = Inflector::slug($parentCategory);
            $modelCategoryParent->save(false);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $modelCategoryParent->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $modelCategoryParent->id;
                $modelTranslate->language = $language;
                $modelTranslate->title = $parentCategory;
                $modelTranslate->meta_title = $parentCategory;
                $modelTranslate->meta_description = $parentCategory;
                $modelTranslate->save(false);
            }

            if(!empty($Brand)) {
                if (!$modelBrandCategory = Category::find()->where(['partner_slug' => Inflector::slug($Brand), 'partner' => $this->partner, 'parent_id' => $modelCategoryParent->id])->one()) {
                    $modelBrandCategory = new Category();
                }
                $modelBrandCategory->parent_id = $modelCategoryParent->id;
                $modelBrandCategory->partner = $this->partner;
                $modelBrandCategory->partner_slug = Inflector::slug($Brand);
                $modelBrandCategory->slug = Inflector::slug($Brand);
                $modelBrandCategory->save(false);
                foreach (Language::getLanguagesAsArray() as $language) {
                    if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $modelBrandCategory->id, 'language' => $language])) {
                        $modelTranslate = new CategoryTranslate();
                    }
                    $modelTranslate->shop_category_id = $modelBrandCategory->id;
                    $modelTranslate->language = $language;
                    $modelTranslate->title = $Brand;
                    $modelTranslate->meta_title = $Brand;
                    $modelTranslate->meta_description = $Brand;
                    $modelTranslate->save(false);
                }
            }

            $parent_id = (!empty($modelBrandCategory->id)) ? $modelBrandCategory->id : $modelCategoryParent->id;

            if (!$modelCategory = Category::find()->where(['partner_slug' => $GroupSlug, 'partner' => $this->partner, 'parent_id' => $parent_id])->one()) {
                $modelCategory = new Category();
            }
            $modelCategory->parent_id = $parent_id;
            $modelCategory->partner = $this->partner;
            $modelCategory->partner_slug = $GroupSlug;
            $modelCategory->slug = $GroupSlug;
            $modelCategory->save(false);
            foreach (Language::getLanguagesAsArray() as $language) {
                if (!$modelTranslate = CategoryTranslate::findOne(['shop_category_id' => $modelCategory->id, 'language' => $language])) {
                    $modelTranslate = new CategoryTranslate();
                }
                $modelTranslate->shop_category_id = $modelCategory->id;
                $modelTranslate->language = $language;
                $modelTranslate->title = $Group;
                $modelTranslate->meta_title = $Group;
                $modelTranslate->meta_description = $Group;
                $modelTranslate->save(false);
            }

            $picture = (!empty($picture)) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . $picture : null;
            $image = "";
            $isPicture = false;
            if (!empty($picture)) {
                $fileData = $this->fileUrlData($picture);
                $image = $fileData['name'];
                $isPicture = true;
            }

            if (!$model = Product::find()->where(['partner' => $this->partner, 'partner_id' => $siteCode, 'image' => $image])->one()) {
                $model = new Product();
            }
            $name = (string)$item->Name;
            if (empty($name)) {
                continue;
            }
            $description = (string)$item->Opisanie;
            $price = (string)$item->Poznica;
            $price = (float)str_replace(' ', '', $price);
            $price += ($price*30)/100;
            $model->code = $siteCode;
            $model->price = null;
            $model->partner = $this->partner;
            $model->partner_id = $siteCode;
            $model->category_id = $modelCategory->id;
            $model->slug = Inflector::slug($name);
            $model->image = $image;
            $model->save(false);
            if (!empty($picture) && is_file($picture) && $this->picLoads) {
                $image = Yii::getAlias('@frontend/web/upload/shop/products/' . $model->id . '.' . $fileData['extension']);
                if(!is_file($image)) {
                    copy($picture, $image);
                    $thumbFilePath = [
                        'thumb' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/thumb_' . $model->id . '.' . $fileData['extension']),
                        'preview' => Yii::getAlias('@frontend/web/upload/shop/products/thumb/preview_' . $model->id . '.' . $fileData['extension']),
                    ];
                    $this->createThumbs($image, $thumbFilePath, $this->thumbs);
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

            if (count($pictures) && $this->picLoads) {
                $ids = [];
                foreach ($pictures as $key => $pic) {
                    if (empty($pic)) {
                        continue;
                    }
                    $picture = (!empty($pic)) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . $pic : null;
                    if (!is_file($picture)) {
                        continue;
                    }
                    $fileData = $this->fileUrlData($picture);
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
                    $image = Yii::getAlias('@frontend/web/upload/shop/products/image/' . $modelImage->id . '.' . $fileData['extension']);
                    if(!is_file($image)) {
                        copy($picture, $image);
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

            $ids = [];
            foreach ($options as $option => $value) {
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

            $Razmer = (string)$item->Razmer;
            if (empty($Razmer)) {
                $Razmer = 1;
            }
            $productIds[$model->id][$Razmer] = [
                'id' => $model->id,
                'price' => $price,
                'code' => $Artikul,
                'ostatok' => $Ostatok
            ];

            $i++;
            if (($i % 500) == 0) {
                echo $i . '; ';
            }
            //if($i==15){break;}
        }
        echo $i . '; ';
        foreach ($productIds as $keyId => $ids) {
            if (count($ids) > 1) {
                $ost = false;
                foreach ($ids as $razmer => $item) {
                    if (!$modelSize = Size::find()->where(['name' => $razmer])->one()) {
                        $modelSize = new Size();
                    }
                    $modelSize->slug = Inflector::slug($razmer);
                    $modelSize->name = $razmer;
                    $modelSize->save(false);

                    if (!$modelProductSize = ProductSize::find()->where(['product_id' => $item['id'], 'size_id' => $modelSize->id])->one()) {
                        $modelProductSize = new ProductSize();
                    }
                    $modelProductSize->product_id = $item['id'];
                    $modelProductSize->size_id = $modelSize->id;
                    $modelProductSize->price = $item['price'];
                    $modelProductSize->code = $item['code'];
                    $modelProductSize->not_available = ($item['ostatok']) ? 0 : 1;
                    if ($item['ostatok'] > 0) {
                        $ost = true;
                    }
                    $modelProductSize->save(false);
                }
                $modelProduct = Product::findOne($keyId);
                $modelProduct->price_old = $item['price'];
                $modelProduct->price = null;
                $modelProduct->not_available = ($ost) ? 0 : 1;
                $modelProduct->save(false);
                $options['Наличие'] = ($ost) ? 'Есть в наличии' : 'Под заказ';
                $this->available($options, $modelProduct);
            } else {
                foreach ($ids as $razmer => $item) {
                    $modelProduct = Product::findOne($item['id']);
                    $modelProduct->price = $item['price'];
                    $modelProduct->code = $item['code'];
                    $modelProduct->not_available = ($item['ostatok']) ? 0 : 1;
                    $modelProduct->save(false);
                    $options['Наличие'] = ($item['ostatok']) ? 'Есть в наличии' : 'Под заказ';
                    $this->available($options, $modelProduct);
                }
            }
        }


//            $modelsCategory = Category::find()->where(['partner' => $this->partner, 'parent_id' => $modelCategoryParent->id])->all();
//            foreach ($modelsCategory as $modelCategory) {
//               if(!Product::find()->where(['category_id' => $modelCategory->id])->count()) {
//                   $modelCategory->delete();
//               }
//            }

        echo 'end; ';
    }


    private function available($options, $model)
    {
        foreach ($options as $option => $value) {
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


}