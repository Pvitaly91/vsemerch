<?php

namespace common\models;

use common\Helpers\ShopImageStorage;
use common\components\MultilingualQuery;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
use navatech\language\Translate;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yiidreamteam\upload\FileUploadBehavior;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property integer $category_id
 * @property string $price
 *
 * @property Image[] $images
 * @property OrderItem[] $orderItems
 * @property Category $category
 */
class Product extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait, traits\minCountOrder, \common\models\traits\EditLink, \common\models\traits\TotobiDiscount, \common\models\traits\Images;
    
    public $transfer_all = 1;
	
	public $size_id;
	
	public $sortTableSize = [
        "S",
        "M",
        "L",
        "XL",
        "XXL",
        "2XL",
        "XXXL",
        "3XL",
        "XXXXL",
        "4XL",
        "5XL",
        "6XL"
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Language::getLanguagesAsArray(),
                'defaultLanguage' => 'ru',
                'langForeignKey' => 'shop_product_id',
                'tableName' => ProductTranslate::tableName(),
                'attributes' => [
                    'title',
                    'description',
                    'meta_title',
                    'meta_description',
                ]
            ],
            [
                'class' => '\yiidreamteam\upload\ImageUploadBehavior',
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 300, 'height' => 300],
                    'preview' => ['width' => 400, 'height' => 400],
                ],
                'filePath' => '@frontend/web/upload/shop/products/[[pk]].[[extension]]',
                'fileUrl' => '/upload/shop/products/[[pk]].[[extension]]',
                'thumbPath' => '@frontend/web/upload/shop/products/thumb/[[profile]]_[[pk]].[[extension]]',
                'thumbUrl' => '/upload/shop/products/thumb/[[profile]]_[[pk]].[[extension]]',
            ]
        ];
    }
 
   function checkAvaiableSize(){
        
        if(!empty($this->size) && count($this->size)>1){
            $not_available = 1;
            foreach ($this->size as $size){
                if($size->not_available == 0){
                    $not_available = 0;
                    break;
                }
            }
            $this->not_available = $not_available;
        }
        
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['category_id', 'action', 'novelty', 'not_available', 'is_main'], 'integer'],
            [['price', 'price_old'], 'number'],
            [['code','sku_group','partner_id','partner'], 'string', 'max' => 250],
            [['filter','transfer_all'], 'safe'],
            [['title_ru', 'category_id'], 'required'],
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
            [['title'], 'required'],
            [['title', 'title_uk','slug'], 'string', 'max' => 255],
            [['description', 'description_uk'], 'safe'],
            [['meta_title', 'meta_title_uk'], 'string', 'max' => 255],
            [['meta_description', 'meta_description_uk'], 'string', 'max' => 255],
        ];

        if ($this->hasAttribute('remote_image_url')) {
            $rules[] = [['remote_image_url'], 'string'];
        }

        return $rules;
    }

    public function beforeSave($insert)
    {
        if( $this->slug == NUll)
            $this->slug = Inflector::slug($this->title_ru);
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = [
            'title' => Yii::t('shop', 'Title'),
            'description' => Yii::t('shop', 'Description'),
            'title_uk' => Yii::t('shop', 'Title_uk'),
            'description_uk' => Yii::t('shop', 'Description_uk'),
           // 'title_en' => Yii::t('shop', 'Title_en'),
         //   'description_en' => Yii::t('shop', 'Description_en'),
            'code' => Yii::t('shop', 'Code'),
            'category_id' => Yii::t('shop', 'Category'),
            'price' => Yii::t('shop', 'Price'),
            'image' => Yii::t('shop', 'Image'),
            'price_old' => Yii::t('shop', 'Price Old'),
            'novelty' => Yii::t('shop', 'Novelty'),
            'action' => Yii::t('shop', 'Action'),
            'not_available' => Yii::t('shop', 'Not available'),
        ];
        return $attributeLabels;
    }
    public function __get($name) {
       /* if($name == "option"){
         
            $items = parent::__get($name);
           //   dd($items[0]->slug_option);
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
            foreach($items as $k => $option ){
              
               // if($option->option == "Кількість у ящику" || $option->option == "Кількість в упаковці" || $option->option == "Вага ящика"){
                 if(in_array($option->slug_option, $unsetOptions)){   
                   unset($items[$k]);
                }else{
                  //   d($option->slug_option." ".$option->option);
                }
            }
            return $items;
        
        }else{
            return parent::__get($name);
        }*/
       
            return parent::__get($name);
       
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasMany(ProductOption::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionTranslate()
    {
        return $this->hasMany(ProductOption::className(), ['product_id' => 'id'])->multilingual();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslate()
    {
        return $this->hasMany(ProductTranslate::className(), ['shop_product_id' => 'id'])->where(['language' => Yii::$app->language]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSize()
    {
        return $this->hasMany(ProductSize::className(), ['product_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPic($attribute, $profile = 'thumb', $emptyUrl = null)
    {
        $file = $this->getThumbFileUrl($attribute, $profile, null);

        if(!$file || (!ShopImageStorage::isPartitionedPartner($this->partner) && !is_file(Yii::getAlias('@frontend/web' . $file)))) {
            $file = $emptyUrl;
        }
        return $file;
    }

    public function getLazyPic($profile = 'preview')
    {
        $profile = in_array($profile, ['original', 'preview', 'thumb'], true) ? $profile : 'preview';
        $extension = ShopImageStorage::extensionFromImageValue($this->image);
        if (!$extension) {
            return '/img/no_image.jpg';
        }

        if ($profile === 'original') {
            return ShopImageStorage::legacyProductImageUrl($this->id, $extension);
        }

        return ShopImageStorage::legacyProductThumbUrl($this->id, $extension, $profile);
    }

    public function getImageFileUrl($attribute, $emptyUrl = null)
    {
        if (!$this->$attribute) {
            return $emptyUrl;
        }

        $extension = ShopImageStorage::extensionFromImageValue($this->$attribute);
        if (!$extension) {
            return $emptyUrl;
        }

        $legacyUrl = ShopImageStorage::legacyProductImageUrl($this->id, $extension);

        if (ShopImageStorage::isPartitionedPartner($this->partner)) {
            return ShopImageStorage::legacyUrlWithPartnerFallback(
                ShopImageStorage::legacyProductImageUrl($this->id, $extension),
                ShopImageStorage::productImageUrl($this->id, $extension, $this->partner),
                $emptyUrl
            );
        }

        return $legacyUrl;
    }

    public function getThumbFileUrl($attribute, $profile = 'thumb', $emptyUrl = null)
    {
        if (!$this->$attribute) {
            return $emptyUrl;
        }

        $extension = ShopImageStorage::extensionFromImageValue($this->$attribute);
        if (!$extension) {
            return $emptyUrl;
        }

        $legacyUrl = ShopImageStorage::legacyProductThumbUrl($this->id, $extension, $profile);

        if (ShopImageStorage::isPartitionedPartner($this->partner)) {
            return ShopImageStorage::legacyUrlWithPartnerFallback(
                ShopImageStorage::legacyProductThumbUrl($this->id, $extension, $profile),
                ShopImageStorage::productThumbUrl($this->id, $extension, $profile, $this->partner),
                $emptyUrl
            );
        }

        return $legacyUrl;
    }

    public function getGalleryImages()
    {
        $images = $this->images;

        if (!ShopImageStorage::isPartitionedPartner($this->partner)) {
            return array_values(array_filter($images, function ($image) {
                return $this->isGalleryImageResolvable($image);
            }));
        }

        $skuCodes = $this->getSkuGroupCodesForGalleryFilter();
        if (!$skuCodes) {
            return array_values(array_filter($images, function ($image) {
                return $this->isGalleryImageResolvable($image);
            }));
        }

        return array_values(array_filter($images, function ($image) use ($skuCodes) {
            $name = pathinfo(parse_url((string)$image->image, PHP_URL_PATH) ?: (string)$image->image, PATHINFO_FILENAME);
            $name = strtoupper(trim($name));

            return ($name === '' || !isset($skuCodes[$name])) && $this->isGalleryImageResolvable($image);
        }));
    }

    private function isGalleryImageResolvable(Image $image)
    {
        if (!$image->image) {
            return false;
        }

        $remote = $image->hasAttribute('remote_image_url') ? trim((string)$image->remote_image_url) : '';
        if ($this->isRemoteImageUrl($remote) || $this->isRemoteImageUrl((string)$image->image)) {
            return true;
        }

        $extension = ShopImageStorage::extensionFromImageValue($image->image);
        if (!$extension) {
            return false;
        }

        $legacyUrl = ShopImageStorage::legacyGalleryImageUrl($image->id, $extension);
        if (is_file(Yii::getAlias('@frontend/web' . $legacyUrl))) {
            return true;
        }

        if (ShopImageStorage::isPartitionedPartner($this->partner)) {
            return is_file(Yii::getAlias('@frontend/web' . ShopImageStorage::galleryImageUrl($image->id, $extension, $this->partner)));
        }

        return false;
    }

    private function isRemoteImageUrl(string $url)
    {
        $scheme = strtolower((string)parse_url(trim($url), PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'ftp'], true);
    }

    private function getSkuGroupCodesForGalleryFilter()
    {
        $skuGroup = trim((string)$this->sku_group);
        if ($skuGroup === '') {
            return [];
        }

        $codes = self::find()
            ->select('code')
            ->where([
                'partner' => $this->partner,
                'sku_group' => $skuGroup,
                'not_active' => 0,
            ])
            ->column();

        $result = [];
        foreach ($codes as $code) {
            $code = strtoupper(trim((string)$code));
            if ($code !== '') {
                $result[$code] = true;
            }
        }

        return $result;
    }


}
