<?php

namespace console\controllers;

use common\Helpers\Partners;
use common\Helpers\ShopImageStorage;
use common\models\CategoriesMap;
use common\models\Language;
use common\models\OptionMap;
use common\models\Product;
use console\components\Controller;
use console\models\Category;
use console\models\Image;
use console\models\ImageTranslate;
use console\models\ProductOption;
use console\models\ProductOptionTranslate;
use console\models\ProductSize;
use console\models\ProductTranslate;
use console\models\Size;
use Yii;
use yii\helpers\Inflector;

class EneySyncController extends Controller
{
    use \common\models\traits\Images;

    public $defaultAction = 'update';

    private const PARTNER = 'eney';
    private const PRODUCTS_XML = 'https://www.eney.com.ua/system/exchanger/ext/more_products_options.xml';
    private const CATEGORIES_XML = 'https://www.eney.com.ua/system/exchanger/more_products_options_dop_category.xml';
    private const IMAGES_XML = 'https://www.eney.com.ua/system/exchanger/more_products_options_dop_image.xml';

    public $dryRun = false;
    public $limit = 0;
    public $loadImages = true;
    public $forceImages = false;
    public $deleteMissingImages = false;
    public $syncOptions = true;
    public $overwriteTranslations = false;
    public $apply = false;
    public $reportPath;

    private $optionMap = [];
    private $categoryByModel;
    private $extraImagesByModel;
    private $skuCodesByGroup = [];
    private $feedCodes = [];
    private $savedIds = [];
    private $reportRows = [];
    private $stats = [
        'feed' => 0,
        'created' => 0,
        'updated' => 0,
        'unchanged' => 0,
        'marked_unavailable' => 0,
        'images' => 0,
        'extra_images' => 0,
        'image_skipped' => 0,
        'extra_image_skipped' => 0,
        'stale_images_deleted' => 0,
        'image_errors' => 0,
        'options' => 0,
        'skipped' => 0,
    ];

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'dryRun',
            'limit',
            'loadImages',
            'forceImages',
            'deleteMissingImages',
            'syncOptions',
            'overwriteTranslations',
            'apply',
            'reportPath',
        ]);
    }

    public function actionUpdate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->dryRun = $this->toBool($this->dryRun);
        $this->loadImages = $this->toBool($this->loadImages);
        $this->forceImages = $this->toBool($this->forceImages);
        $this->deleteMissingImages = $this->toBool($this->deleteMissingImages);
        $this->syncOptions = $this->toBool($this->syncOptions);
        $this->overwriteTranslations = $this->toBool($this->overwriteTranslations);
        $this->limit = (int)$this->limit;

        $this->initOptionMap();
        $products = $this->loadProductsFeed();

        echo 'start ' . date('d-m-Y H:i:s') . PHP_EOL;
        echo 'feed products: ' . count($products) . PHP_EOL;
        if ($this->dryRun) {
            echo 'mode: dry-run' . PHP_EOL;
        }

        $i = 0;
        foreach ($products as $product) {
            if ($this->limit > 0 && $i >= $this->limit) {
                break;
            }
            $this->syncProduct($product);
            $i++;
        }

        $this->markMissingFeedProductsUnavailable();
        $reportPath = $this->writeUpdateReport();

        echo json_encode($this->stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        if ($reportPath) {
            echo 'report: ' . $reportPath . PHP_EOL;
        }
        echo 'end ' . date('d-m-Y H:i:s') . PHP_EOL;
    }

    public function actionGarbage()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->apply = $this->toBool($this->apply);
        $feedCodes = array_fill_keys(array_keys($this->loadProductsFeed()), true);
        $candidates = [];

        $products = Product::find()
            ->where(['partner' => self::PARTNER, 'not_available' => 1])
            ->all();

        foreach ($products as $product) {
            $code = trim((string)$product->code);
            if ($code !== '' && isset($feedCodes[$code])) {
                continue;
            }

            if (!$this->hasLocalProductImages($product)) {
                $candidates[] = $product;
            }
        }

        echo 'candidate count: ' . count($candidates) . PHP_EOL;
        if (!$this->apply) {
            echo 'mode: dry-run, pass --apply=1 to delete' . PHP_EOL;
        }

        $reportRows = [];
        foreach ($candidates as $product) {
            $reportRows[] = $this->makeGarbageReportRow($product);
            echo implode("\t", [
                $product->id,
                $product->code,
                $product->partner_id,
                'http://vsemerch.loc/shop/product/' . $product->slug . '-' . $product->id,
            ]) . PHP_EOL;

            if ($this->apply) {
                $this->deleteProductWithRelations($product);
            }
        }

        $reportPath = $this->writeGarbageReport($reportRows);
        if ($reportPath) {
            echo 'report: ' . $reportPath . PHP_EOL;
        }
    }

    private function syncProduct(array $data)
    {
        $this->stats['feed']++;

        $model = $this->findExistingProduct($data);
        $isNew = $model === null;
        if ($isNew) {
            $model = new Product();
            $model->partner = self::PARTNER;
            $model->code = $data['code'];
            $model->partner_id = $data['partner_id'];
            $model->category_id = $this->resolveCategoryId($data);
            $model->sku_group = $data['sku_group'];
            $model->slug = $this->makeProductSlug($data['title_uk'], $data['code']);
            $model->not_active = 0;
            $model->is_main = 1;
        }

        $before = $this->snapshotProduct($model);
        $changed = $isNew;
        $changed = $this->assignIfChanged($model, 'price', $data['price']) || $changed;
        $changed = $this->assignIfChanged($model, 'not_available', $data['not_available']) || $changed;
        $changed = $this->assignIfChanged($model, 'image', $data['image']) || $changed;
        $changed = $this->assignIfAttributeChanged($model, 'remote_image_url', $data['image']) || $changed;
        $changed = $this->assignIfChanged($model, 'partner', self::PARTNER) || $changed;
        $changed = $this->assignIfChanged($model, 'code', $data['code']) || $changed;
        if ((string)$model->not_active !== '0') {
            $model->not_active = 0;
            $changed = true;
        }

        $action = $isNew ? 'created' : ($changed ? 'updated' : 'unchanged');

        if ($this->dryRun) {
            $this->addReportRow($action, $model, $data, $before);
            $this->stats[$action]++;
            return;
        }

        if (!$model->save(false)) {
            $this->stats['skipped']++;
            echo 'save failed for code ' . $data['code'] . PHP_EOL;
            return;
        }

        $this->savedIds[] = (int)$model->id;
        $this->addReportRow($action, $model, $data, $before);
        $this->stats[$action]++;

        $this->syncTranslations($model, $data, $isNew);

        if ($this->syncOptions) {
            $this->syncProductOptions($model, $data);
        }

        if ($this->loadImages) {
            $this->syncMainImage($model, $data['image'], $before['image'] ?? '');
        }
        $this->syncExtraImages($model, $data['code']);
    }

    private function loadProductsFeed()
    {
        $xml = $this->loadXml(self::PRODUCTS_XML);
        $products = [];
        $groupMain = [];

        foreach ($xml->product as $item) {
            $code = trim((string)$item['model']);
            if ($code === '') {
                continue;
            }

            $optionId = trim((string)$item['option_id']);
            $productId = trim((string)$item->product_id);
            $titleUk = trim(str_replace('ENEY', '', (string)$item->name_ua));
            $titleRu = trim(str_replace('ENEY', '', (string)$item->name_ru));
            if ($titleRu === '') {
                $titleRu = $titleUk;
            }

            $optionName = trim((string)$item->option_name_ua);
            $skuGroup = $this->makeSkuGroup($code, $productId, $optionName);
            $quantity = (int)$item->quantity;
            $notAvailable = $quantity > 0 ? 0 : 1;

            $data = [
                'code' => $code,
                'partner_id' => $optionId !== '' ? $optionId : ($productId !== '' ? $productId : $code),
                'xml_option_id' => $optionId,
                'xml_product_id' => $productId,
                'title_uk' => $titleUk,
                'title_ru' => $titleRu,
                'price' => (float)$item->price,
                'quantity' => $quantity,
                'not_available' => $notAvailable,
                'image' => $this->normalizeImageUrl((string)$item->image),
                'option_name_ua' => $optionName,
                'sku_group' => $skuGroup,
                'attributes' => $this->parseAttributes($item, $notAvailable, $titleUk, $optionName),
            ];

            if (!isset($groupMain[$skuGroup])) {
                $groupMain[$skuGroup] = $code;
            }
            $data['is_main'] = $groupMain[$skuGroup] === $code ? 1 : 0;

            $products[$code] = $data;
            $this->feedCodes[$code] = true;
        }

        return $products;
    }

    private function findExistingProduct(array $data)
    {
        $model = Product::find()
            ->where(['partner' => self::PARTNER])
            ->andWhere('TRIM([[code]]) = :code', [':code' => $data['code']])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if ($model || $data['xml_option_id'] === '') {
            return $model;
        }

        return Product::find()
            ->where(['partner' => self::PARTNER, 'partner_id' => $data['xml_option_id']])
            ->one();
    }

    private function markMissingFeedProductsUnavailable()
    {
        if ($this->dryRun) {
            $count = 0;
            foreach (Product::find()->where(['partner' => self::PARTNER])->all() as $product) {
                $code = trim((string)$product->code);
                if ($code === '' || !isset($this->feedCodes[$code])) {
                    if ((int)$product->not_available !== 1) {
                        $this->addReportRow('marked_unavailable', $product, [
                            'code' => trim((string)$product->code),
                            'title_uk' => '',
                            'price' => $product->price,
                            'quantity' => 0,
                            'not_available' => 1,
                            'image' => $product->image,
                            'xml_product_id' => '',
                            'xml_option_id' => '',
                        ], $this->snapshotProduct($product));
                        $count++;
                    }
                }
            }
            $this->stats['marked_unavailable'] = $count;
            return;
        }

        foreach (Product::find()->where(['partner' => self::PARTNER])->all() as $product) {
            $code = trim((string)$product->code);
            if ($code === '' || !isset($this->feedCodes[$code])) {
                if ((int)$product->not_available !== 1) {
                    $this->addReportRow('marked_unavailable', $product, [
                        'code' => $code,
                        'title_uk' => '',
                        'price' => $product->price,
                        'quantity' => 0,
                        'not_available' => 1,
                        'image' => $product->image,
                        'xml_product_id' => '',
                        'xml_option_id' => '',
                    ], $this->snapshotProduct($product));
                    $product->not_available = 1;
                    $product->save(false);
                    $this->stats['marked_unavailable']++;
                }
            }
        }
    }

    private function syncTranslations(Product $product, array $data, $isNew)
    {
        foreach (Language::getLanguagesAsArray() as $language) {
            $translation = ProductTranslate::findOne([
                'shop_product_id' => $product->id,
                'language' => $language,
            ]);
            if (!$translation) {
                $translation = new ProductTranslate();
                $translation->shop_product_id = $product->id;
                $translation->language = $language;
            } elseif (!$this->overwriteTranslations) {
                continue;
            }

            $title = $language === 'ru' ? $data['title_ru'] : $data['title_uk'];
            if ($title === '') {
                $title = $data['title_uk'];
            }
            $translation->title = $title;
            if ($isNew || $this->overwriteTranslations) {
                $translation->description = $title;
                $translation->meta_title = $title;
                $translation->meta_description = $title;
            }
            $translation->save(false);
        }
    }

    private function syncProductOptions(Product $product, array $data)
    {
        $ids = [];

        foreach ($data['attributes']['uk'] as $option => $valueUk) {
            $valueUk = trim((string)$valueUk);
            if ($valueUk === '') {
                continue;
            }

            $slugOption = $this->getOptionSlug($option);
            $slug = Inflector::slug(str_replace('м²', 'м2', $valueUk));

            $model = ProductOption::find()
                ->where([
                    'product_id' => $product->id,
                    'slug_option' => $slugOption,
                    'slug' => $slug,
                ])
                ->one();
            if (!$model) {
                $model = new ProductOption();
            }

            $model->product_id = $product->id;
            $model->is_filter = true;
            $model->slug_option = $slugOption;
            $model->slug = $slug;
            $model->partner = self::PARTNER;
            $model->save(false);
            $ids[] = $model->id;

            foreach (Language::getLanguagesAsArray() as $language) {
                $translation = ProductOptionTranslate::findOne([
                    'shop_product_option_id' => $model->id,
                    'language' => $language,
                ]);
                if (!$translation) {
                    $translation = new ProductOptionTranslate();
                }
                $translation->shop_product_option_id = $model->id;
                $translation->language = $language;
                $translation->option = $this->getOptionLabel($option, $language);
                $value = $data['attributes'][$language][$option] ?? $valueUk;
                $translation->value = str_replace('м²', 'м2', $value);
                $translation->save(false);
            }
        }

        if ($ids) {
            ProductOption::deleteAll([
                'AND',
                'product_id=:product_id',
                ['not in', 'id', $ids],
            ], [':product_id' => $product->id]);
        }

        $this->stats['options']++;
    }

    private function syncMainImage(Product $product, $url, $oldUrl = '')
    {
        if (!$url) {
            return;
        }

        $fileData = $this->fileUrlData($url);
        if (empty($fileData['extension'])) {
            $this->stats['image_errors']++;
            return;
        }

        $partner = $product->partner ?: self::PARTNER;
        $imagePath = ShopImageStorage::productImagePath($product->id, $fileData['extension'], $partner);
        $thumbPaths = [
            'thumb' => ShopImageStorage::productThumbPath($product->id, $fileData['extension'], 'thumb', $partner),
            'preview' => ShopImageStorage::productThumbPath($product->id, $fileData['extension'], 'preview', $partner),
        ];
        $sourceChanged = $this->imageSourceChanged($oldUrl, $url);

        if ($this->forceImages) {
            $this->deleteMainImageFiles($product);
        }

        if (!$this->forceImages && !$sourceChanged && $this->imageFilesComplete($imagePath, $thumbPaths)) {
            $this->stats['image_skipped']++;
            return;
        }

        if (!$this->forceImages && !$sourceChanged && is_file($imagePath)) {
            $this->createMissingThumbs($imagePath, $thumbPaths, $this->thumbs);
            $this->stats['image_skipped']++;
            return;
        }

        if ($this->saveImageSet($url, $imagePath, $thumbPaths, $this->thumbs)) {
            $this->stats['images']++;
        } else {
            $this->stats['image_errors']++;
        }
    }

    private function syncExtraImages(Product $product, $code)
    {
        $images = $this->getExtraImagesByModel();
        if (!isset($images[$code]) || !$images[$code]) {
            return;
        }

        $syncedIds = [];
        $colorImageSkipped = 0;
        foreach ($images[$code] as $url) {
            if ($this->isSkuGroupColorImage($product, $url)) {
                $colorImageSkipped++;
                $this->stats['extra_image_skipped']++;
                continue;
            }

            $image = $this->syncExtraImage($product, $url);
            if ($image) {
                $syncedIds[] = (int)$image->id;
            }
        }

        if ($this->deleteMissingImages && ($syncedIds || $colorImageSkipped === count($images[$code]))) {
            $staleImages = Image::find()
                ->where(['product_id' => $product->id])
                ->andFilterWhere($syncedIds ? ['not in', 'id', $syncedIds] : [])
                ->all();
            foreach ($staleImages as $image) {
                $this->deleteImageFiles($image);
                ImageTranslate::deleteAll(['shop_product_image_id' => $image->id]);
                $image->delete();
                $this->stats['stale_images_deleted']++;
            }
        }
    }

    private function syncExtraImage(Product $product, $url)
    {
        $fileData = $this->fileUrlData($url);
        if (empty($fileData['name']) || empty($fileData['extension'])) {
            $this->stats['image_errors']++;
            return null;
        }

        $isNewImage = false;
        $image = Image::findOne(['product_id' => $product->id, 'image' => $fileData['name']]);
        if (!$image) {
            $image = new Image();
            $image->product_id = $product->id;
            $image->image = $fileData['name'];
            $isNewImage = true;
        }
        $imageChanged = $isNewImage;
        $imageChanged = $this->assignIfAttributeChanged($image, 'remote_image_url', $url) || $imageChanged;
        if ($imageChanged) {
            $image->save(false);
        }

        $this->syncImageTranslations($image);

        if (!$this->loadImages) {
            $this->stats['extra_image_skipped']++;
            return $image;
        }

        $partner = $product->partner ?: self::PARTNER;
        $imagePath = ShopImageStorage::galleryImagePath($image->id, $fileData['extension'], $partner);
        $thumbPaths = [
            'thumb' => ShopImageStorage::galleryThumbPath($image->id, $fileData['extension'], 'thumb', $partner),
            'ico' => ShopImageStorage::galleryThumbPath($image->id, $fileData['extension'], 'ico', $partner),
        ];

        if ($this->forceImages) {
            $this->deleteImageFiles($image);
        }

        if (!$this->forceImages && $this->imageFilesComplete($imagePath, $thumbPaths)) {
            $this->stats['extra_image_skipped']++;
            return $image;
        }

        if (!$this->forceImages && is_file($imagePath)) {
            $this->createMissingThumbs($imagePath, $thumbPaths, $this->ico);
            $this->stats['extra_image_skipped']++;
            return $image;
        }

        if ($this->saveImageSet($url, $imagePath, $thumbPaths, $this->ico)) {
            $this->stats['extra_images']++;
        } else {
            $this->stats['image_errors']++;
            if ($isNewImage && !is_file($imagePath)) {
                ImageTranslate::deleteAll(['shop_product_image_id' => $image->id]);
                $image->delete();
                return null;
            }
        }

        return $image;
    }

    private function isSkuGroupColorImage(Product $product, $url)
    {
        $skuGroup = trim((string)$product->sku_group);
        if ($skuGroup === '') {
            return false;
        }

        $fileData = $this->fileUrlData($url);
        if (empty($fileData['name'])) {
            return false;
        }

        $imageCode = strtoupper(pathinfo($fileData['name'], PATHINFO_FILENAME));
        if ($imageCode === '') {
            return false;
        }

        if (!array_key_exists($skuGroup, $this->skuCodesByGroup)) {
            $codes = Product::find()
                ->select('code')
                ->where([
                    'partner' => self::PARTNER,
                    'sku_group' => $skuGroup,
                    'not_active' => 0,
                ])
                ->column();

            $this->skuCodesByGroup[$skuGroup] = [];
            foreach ($codes as $code) {
                $code = strtoupper(trim((string)$code));
                if ($code !== '') {
                    $this->skuCodesByGroup[$skuGroup][$code] = true;
                }
            }
        }

        return isset($this->skuCodesByGroup[$skuGroup][$imageCode]);
    }

    private function syncImageTranslations(Image $image)
    {
        foreach (Language::getLanguagesAsArray() as $language) {
            if (!$translate = ImageTranslate::findOne(['shop_product_image_id' => $image->id, 'language' => $language])) {
                $translate = new ImageTranslate();
            }
            $translate->shop_product_image_id = $image->id;
            $translate->language = $language;
            $translate->alt = '';
            $translate->save(false);
        }
    }

    private function saveImageSet($url, $imagePath, array $thumbPaths, array $thumbs)
    {
        if (!$this->isFile($url)) {
            return false;
        }

        if (!$this->normalizeOriginalFoto($url, $imagePath)) {
            return false;
        }

        $this->createThumbs($imagePath, $thumbPaths, $thumbs);
        return is_file($imagePath);
    }

    private function createMissingThumbs($imagePath, array $thumbPaths, array $thumbs)
    {
        foreach ($thumbPaths as $thumbPath) {
            if (!is_file($thumbPath)) {
                $this->createThumbs($imagePath, $thumbPaths, $thumbs);
                return;
            }
        }
    }

    private function imageFilesComplete($imagePath, array $thumbPaths)
    {
        if (!is_file($imagePath)) {
            return false;
        }

        foreach ($thumbPaths as $thumbPath) {
            if (!is_file($thumbPath)) {
                return false;
            }
        }

        return true;
    }

    private function imageSourceChanged($oldUrl, $newUrl)
    {
        $old = $this->fileUrlData($oldUrl);
        $new = $this->fileUrlData($newUrl);

        if (empty($old['name']) || empty($new['name'])) {
            return false;
        }

        return $old['name'] !== $new['name'];
    }

    private function parseAttributes($item, $notAvailable, $titleUk, $optionName)
    {
        $attributes = ['uk' => [], 'ru' => [], 'en' => []];

        foreach ($item->attributes->children() as $attribute) {
            $name = trim($attribute->getName());
            $value = trim((string)$attribute);
            if ($name === '' || $value === '') {
                continue;
            }

            if (substr($name, -3) === '_ua') {
                $base = substr($name, 0, -3);
                $attributes['uk'][$base] = $value;
                continue;
            }
            if (substr($name, -3) === '_ru') {
                $base = substr($name, 0, -3);
                $attributes['ru'][$base] = $value;
                continue;
            }

            $attributes['uk'][$name] = $value;
            $attributes['ru'][$name] = $value;
        }

        if ($optionName !== '') {
            if ($this->isSizeValue($optionName)) {
                $attributes['uk']['size_clothes'] = $optionName;
                $attributes['ru']['size_clothes'] = $optionName;
            } else {
                $attributes['uk']['color'] = $optionName;
                $attributes['ru']['color'] = $optionName;
            }
        }

        $this->applyGenderAttribute($attributes, $titleUk);
        Partners::availableProp($notAvailable, $attributes);

        return $attributes;
    }

    private function applyGenderAttribute(array &$attributes, $titleUk)
    {
        if (strpos($titleUk, 'жіноче') !== false || strpos($titleUk, 'жіноч') !== false) {
            $attributes['uk']['Стать'] = 'для жінок';
            $attributes['ru']['Стать'] = 'для жінок';
        } elseif (strpos($titleUk, 'чоло') !== false) {
            $attributes['uk']['Стать'] = 'для чоловіків';
            $attributes['ru']['Стать'] = 'для чоловіків';
        } elseif (strpos($titleUk, 'унісекс') !== false) {
            $attributes['uk']['Стать'] = 'унісекс';
            $attributes['ru']['Стать'] = 'унісекс';
        }
    }

    private function resolveCategoryId(array $data)
    {
        $slug = $this->getCategorySlugForModel($data['code']);
        if ($slug) {
            $map = CategoriesMap::find()
                ->where(['partner' => self::PARTNER, 'partner_slug' => $slug])
                ->one();
            if ($map && $map->site_slug) {
                $siteCategory = Category::find()
                    ->where(['slug' => $map->site_slug])
                    ->andWhere(['!=', 'partner', self::PARTNER])
                    ->one();
                if ($siteCategory) {
                    return $siteCategory->id;
                }
            }

            $category = Category::find()
                ->where(['slug' => $slug])
                ->andWhere(['!=', 'partner', self::PARTNER])
                ->one();
            if ($category) {
                return $category->id;
            }
        }

        $root = $this->getOrCreateEneyRootCategory();
        return $root->id;
    }

    private function getOrCreateEneyRootCategory()
    {
        $model = Category::find()->where(['slug' => self::PARTNER, 'partner' => self::PARTNER])->one();
        if ($model) {
            return $model;
        }

        $model = new Category();
        $model->slug = self::PARTNER;
        $model->partner_slug = self::PARTNER;
        $model->partner = self::PARTNER;
        $model->active = 1;
        if (!$this->dryRun) {
            $model->save(false);
        }

        return $model;
    }

    private function getCategorySlugForModel($code)
    {
        if ($this->categoryByModel === null) {
            $this->categoryByModel = [];
            $xml = @simplexml_load_file(self::CATEGORIES_XML);
            if ($xml) {
                foreach ($xml->product as $product) {
                    $model = trim((string)$product['model']);
                    if ($model === '') {
                        continue;
                    }
                    $slug = null;
                    foreach ($product->categories->category as $category) {
                        $name = trim((string)$category->category_ua);
                        if ($name !== '') {
                            $slug = Inflector::slug($name);
                        }
                    }
                    if ($slug) {
                        $this->categoryByModel[$model] = $slug;
                    }
                }
            }
        }

        return $this->categoryByModel[$code] ?? null;
    }

    private function getExtraImagesByModel()
    {
        if ($this->extraImagesByModel === null) {
            $this->extraImagesByModel = [];
            $xml = @simplexml_load_file(self::IMAGES_XML);
            if ($xml) {
                foreach ($xml->product as $product) {
                    $model = trim((string)$product['model']);
                    if ($model === '') {
                        continue;
                    }
                    foreach ($product->images->image as $image) {
                        $url = $this->normalizeImageUrl((string)$image);
                        if ($url) {
                            $this->extraImagesByModel[$model][] = $url;
                        }
                    }
                }
            }
        }

        return $this->extraImagesByModel;
    }

    private function hasLocalProductImages(Product $product)
    {
        if ($this->hasLocalMainImage($product)) {
            return true;
        }

        foreach (Image::find()->where(['product_id' => $product->id])->all() as $image) {
            if ($this->hasLocalGalleryImage($image)) {
                return true;
            }
        }

        return false;
    }

    private function hasLocalMainImage(Product $product)
    {
        $extension = $this->extensionFromImageValue($product->image);
        if (!$extension) {
            return false;
        }

        return $this->hasAnyFile($this->mainImagePaths($product, $extension));
    }

    private function hasLocalGalleryImage(Image $image)
    {
        $extension = $this->extensionFromImageValue($image->image);
        if (!$extension) {
            return false;
        }

        return $this->hasAnyFile($this->galleryImagePaths($image, $extension));
    }

    private function deleteProductWithRelations(Product $product)
    {
        foreach (Image::find()->where(['product_id' => $product->id])->all() as $image) {
            $this->deleteImageFiles($image);
            ImageTranslate::deleteAll(['shop_product_image_id' => $image->id]);
            $image->delete();
        }

        $optionIds = ProductOption::find()
            ->select('id')
            ->where(['product_id' => $product->id])
            ->column();
        if ($optionIds) {
            ProductOptionTranslate::deleteAll(['shop_product_option_id' => $optionIds]);
        }
        ProductOption::deleteAll(['product_id' => $product->id]);
        ProductSize::deleteAll(['product_id' => $product->id]);
        ProductTranslate::deleteAll(['shop_product_id' => $product->id]);

        $this->deleteMainImageFiles($product);
        $product->delete();
    }

    private function deleteMainImageFiles(Product $product)
    {
        $extension = $this->extensionFromImageValue($product->image);
        if (!$extension) {
            return;
        }

        foreach ($this->mainImagePaths($product, $extension) as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function deleteImageFiles(Image $image)
    {
        $extension = $this->extensionFromImageValue($image->image);
        if (!$extension) {
            return;
        }

        foreach ($this->galleryImagePaths($image, $extension) as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function mainImagePaths(Product $product, $extension)
    {
        $partner = $product->partner ?: self::PARTNER;

        return $this->uniquePaths([
            ShopImageStorage::productImagePath($product->id, $extension, $partner),
            ShopImageStorage::productThumbPath($product->id, $extension, 'preview', $partner),
            ShopImageStorage::productThumbPath($product->id, $extension, 'thumb', $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyProductImageUrl($product->id, $extension)),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyProductThumbUrl($product->id, $extension, 'preview')),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyProductThumbUrl($product->id, $extension, 'thumb')),
        ]);
    }

    private function galleryImagePaths(Image $image, $extension)
    {
        $partner = ShopImageStorage::partnerForProductId($image->product_id) ?: self::PARTNER;

        return $this->uniquePaths([
            ShopImageStorage::galleryImagePath($image->id, $extension, $partner),
            ShopImageStorage::galleryThumbPath($image->id, $extension, 'ico', $partner),
            ShopImageStorage::galleryThumbPath($image->id, $extension, 'thumb', $partner),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryImageUrl($image->id, $extension)),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryThumbUrl($image->id, $extension, 'ico')),
            ShopImageStorage::urlToPath(ShopImageStorage::legacyGalleryThumbUrl($image->id, $extension, 'thumb')),
        ]);
    }

    private function hasAnyFile(array $paths)
    {
        foreach ($paths as $path) {
            if (is_file($path)) {
                return true;
            }
        }

        return false;
    }

    private function uniquePaths(array $paths)
    {
        return array_values(array_unique($paths));
    }

    private function initOptionMap()
    {
        foreach (OptionMap::find()->where(['partner' => self::PARTNER])->all() as $item) {
            $this->optionMap[$item->partner_slug] = $item->site_slug;
        }

        $this->optionMap['grouppananeseniya'] = 'grupa-nanesenna';
        $this->optionMap['category'] = 'stat';
        $this->optionMap['sex'] = 'stat';
        $this->optionMap['brand'] = 'tm';
        $this->optionMap['trademark'] = 'tm';
        $this->optionMap['color'] = 'kolir';
        $this->optionMap['countryofmanufacture'] = 'country';
    }

    private function getOptionSlug($option)
    {
        $slug = Inflector::slug($option);
        return $this->optionMap[$slug] ?? $slug;
    }

    private function getOptionLabel($option, $language)
    {
        $labels = [
            'color' => ['uk' => 'Колір', 'ru' => 'Цвет'],
            'size_clothes' => ['uk' => 'Розмір', 'ru' => 'Размер'],
            'category' => ['uk' => 'Стать', 'ru' => 'Пол'],
            'sex' => ['uk' => 'Стать', 'ru' => 'Пол'],
            'brand' => ['uk' => 'TM', 'ru' => 'TM'],
            'trademark' => ['uk' => 'TM', 'ru' => 'TM'],
        ];

        return $labels[$option][$language] ?? $option;
    }

    private function makeSkuGroup($code, $productId, $optionName)
    {
        if ($this->isSizeValue($optionName) && strlen($code) >= 6) {
            return substr($code, 0, 6);
        }

        return $productId !== '' ? $productId : $code;
    }

    private function isSizeValue($value)
    {
        return in_array($value, [
            'S',
            'M',
            'L',
            'XL',
            '2XL',
            '3XL',
            '4XL',
            '5XL',
            '6XL',
            'XXL',
            '2XXL',
            '3XXL',
            '4XXL',
            '5XXL',
            '6XXL',
        ], true);
    }

    private function makeProductSlug($title, $code)
    {
        $slug = Inflector::slug($title);
        return $slug !== '' ? $slug : Inflector::slug($code);
    }

    private function normalizeImageUrl($url)
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        return str_replace('http://', 'https://', $url);
    }

    private function loadXml($url)
    {
        $context = stream_context_create($this->arrContextOptions);
        $lastError = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $contents = @file_get_contents($url, false, $context);
            if ($contents !== false && $contents !== '') {
                $xml = @simplexml_load_string($contents);
                if ($xml !== false) {
                    return $xml;
                }
                $lastError = 'invalid XML';
            } else {
                $lastError = 'empty response';
            }

            sleep($attempt);
        }

        throw new \RuntimeException('Cannot load XML: ' . $url . ' (' . $lastError . ')');
    }

    private function extensionFromImageValue($image)
    {
        return ShopImageStorage::extensionFromImageValue($image);
    }

    private function snapshotProduct($model)
    {
        return [
            'id' => $model->id,
            'slug' => $model->slug,
            'code' => $model->code,
            'partner_id' => $model->partner_id,
            'price' => $model->price,
            'not_available' => $model->not_available,
            'not_active' => $model->not_active,
            'image' => $model->image,
        ];
    }

    private function addReportRow($action, $model, array $data, array $before)
    {
        $after = $this->snapshotProduct($model);
        $changes = [];

        foreach (['price', 'not_available', 'not_active', 'image', 'code'] as $field) {
            if ((string)($before[$field] ?? '') !== (string)($after[$field] ?? '')) {
                $changes[] = $field;
            }
        }

        $this->reportRows[] = [
            'action' => $action,
            'id' => $after['id'],
            'title' => $data['title_uk'] ?? '',
            'code' => $after['code'] ?: ($data['code'] ?? ''),
            'partner_id' => $after['partner_id'],
            'local_url' => $this->makeLocalProductUrl($after),
            'old_price' => $before['price'],
            'new_price' => $after['price'],
            'old_not_available' => $before['not_available'],
            'new_not_available' => $after['not_available'],
            'old_not_active' => $before['not_active'],
            'new_not_active' => $after['not_active'],
            'xml_quantity' => $data['quantity'] ?? '',
            'xml_product_id' => $data['xml_product_id'] ?? '',
            'xml_option_id' => $data['xml_option_id'] ?? '',
            'old_image' => $before['image'],
            'new_image' => $after['image'],
            'changes' => implode(',', $changes),
        ];
    }

    private function writeUpdateReport()
    {
        if (!$this->reportRows) {
            return null;
        }

        $path = $this->resolveReportPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $handle = fopen($path, 'w');
        $headers = [
            'action',
            'id',
            'title',
            'code',
            'partner_id',
            'local_url',
            'old_price',
            'new_price',
            'old_not_available',
            'new_not_available',
            'old_not_active',
            'new_not_active',
            'xml_quantity',
            'xml_product_id',
            'xml_option_id',
            'old_image',
            'new_image',
            'changes',
        ];
        fputcsv($handle, $headers);

        foreach ($this->reportRows as $row) {
            fputcsv($handle, array_map(function ($key) use ($row) {
                return $row[$key] ?? '';
            }, $headers));
        }

        fclose($handle);
        return realpath($path) ?: $path;
    }

    private function makeGarbageReportRow(Product $product)
    {
        return [
            'action' => $this->apply ? 'deleted' : 'candidate',
            'id' => $product->id,
            'title' => $this->getProductTitle($product->id),
            'code' => trim((string)$product->code),
            'partner_id' => $product->partner_id,
            'not_available' => $product->not_available,
            'not_active' => $product->not_active,
            'local_url' => 'http://vsemerch.loc/shop/product/' . $product->slug . '-' . $product->id,
            'reason' => 'not_available=1; code missing from current Eney XML; no local product images',
            'image' => $product->image,
        ];
    }

    private function writeGarbageReport(array $rows)
    {
        if (!$rows) {
            return null;
        }

        $path = $this->resolveGarbageReportPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $headers = [
            'action',
            'id',
            'title',
            'code',
            'partner_id',
            'not_available',
            'not_active',
            'local_url',
            'reason',
            'image',
        ];

        $handle = fopen($path, 'w');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, array_map(function ($key) use ($row) {
                return $row[$key] ?? '';
            }, $headers));
        }
        fclose($handle);

        return realpath($path) ?: $path;
    }

    private function resolveGarbageReportPath()
    {
        if ($this->reportPath) {
            return Yii::getAlias($this->reportPath);
        }

        $mode = $this->apply ? 'delete' : 'dry-run';
        return Yii::getAlias('@runtime/reports/eney_garbage_' . $mode . '_' . date('Ymd_His') . '.csv');
    }

    private function getProductTitle($productId)
    {
        $translation = ProductTranslate::findOne([
            'shop_product_id' => $productId,
            'language' => 'uk',
        ]);

        return $translation ? $translation->title : '';
    }

    private function resolveReportPath()
    {
        if ($this->reportPath) {
            return Yii::getAlias($this->reportPath);
        }

        $mode = $this->dryRun ? 'dry-run' : 'update';
        return Yii::getAlias('@runtime/reports/eney_sync_' . $mode . '_' . date('Ymd_His') . '.csv');
    }

    private function makeLocalProductUrl(array $product)
    {
        if (empty($product['id']) || empty($product['slug'])) {
            return '';
        }

        return 'http://vsemerch.loc/shop/product/' . $product['slug'] . '-' . $product['id'];
    }

    private function assignIfChanged($model, $attribute, $value)
    {
        if ((string)$model->$attribute === (string)$value) {
            return false;
        }

        $model->$attribute = $value;
        return true;
    }

    private function assignIfAttributeChanged($model, $attribute, $value)
    {
        if (!method_exists($model, 'hasAttribute') || !$model->hasAttribute($attribute)) {
            return false;
        }

        return $this->assignIfChanged($model, $attribute, $value);
    }

    private function toBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
