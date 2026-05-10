<?php

namespace common\models;

use common\components\MultilingualQuery;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $slug
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property Product[] $products
 */
class Category extends ActiveRecord
{
    use \common\models\traits\EditLink;
    public $copy_category;

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Language::getLanguagesAsArray(),
                'defaultLanguage' => 'ru',
                'langForeignKey' => 'shop_category_id',
                'tableName' => CategoryTranslate::tableName(),
                'attributes' => [
                    'title',
                    'body',
                    'seo',
                    'meta_title',
                    'meta_description',
                ]
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title_ru',
                'slugAttribute' => 'slug'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['parent_id', 'copy_category','active'], 'integer'],
            [['title'], 'required'],
            [['title', 'title_uk', 'link','min_quantity'], 'string', 'max' => 255],
            [['body', 'body_uk', 'catalog_id','sort'], 'safe'],
            [['seo', 'seo_uk'], 'safe'],
            [['meta_title', 'meta_title_uk'], 'string', 'max' => 255],
            [['meta_description', 'meta_description_uk'], 'string', 'max' => 255],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = [
            'parent_id' => Yii::t('shop', 'Parent'),
            'copy_category' => Yii::t('shop', 'Copy Category'),
            'title' => Yii::t('shop', 'Title'),
            'body' => Yii::t('shop', 'Body'),
            'title_uk' => Yii::t('shop', 'Title_uk'),
            'body_uk' => Yii::t('shop', 'Body_uk'),
            'title_en' => Yii::t('shop', 'Title_en'),
            'body_en' => Yii::t('shop', 'Body_en'),
            'min_quantity' =>  Yii::t('shop', 'Min quantity'),
        ];
        return $attributeLabels;
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslate()
    {
        return $this->hasMany(CategoryTranslate::className(), ['shop_category_id' => 'id']);
    }

    public static function asArray()
    {
        $categories = [];
        $models = Category::find()->multilingual()->all();
        foreach ($models as $model) {
            $categories[$model->id] = [
                'id' => $model->id,
                'parent_id' => $model->parent_id,
                'name' => $model->title,
            ];
        }
        return $categories;
    }
    public function save($runValidation = true, $attributeNames = null,$flag = false) {
        if(($result = parent::save($runValidation, $attributeNames)) == true && $flag == false){
            
            if($this->active == 0){
                \common\Helpers\Utils::deactivProductsByCatid($this->id);
            }elseif($this->active > 0){
                \common\Helpers\Utils::activProductsByCatid($this->id);
            }
        }
        return $result;
    }
    
    public function copyProductsWithCategory()
    {
        if(!empty($this->copy_category)) {
            $productModels = Product::find()->where(['category_id' => $this->copy_category])->multilingual()->all();
            foreach ($productModels as $productModel) {
                $model = new Product();
                $model->category_id = $this->id;
                $model->code = $productModel->code;
                $model->price = $productModel->price;
                $model->price_old = $productModel->price_old;
                $model->title = $productModel->title;
                $model->title_uk = $productModel->title_uk;
                $model->title_en = $productModel->title_en;
                $model->description = $productModel->description;
                $model->description_uk = $productModel->description_uk;
                $model->description_en = $productModel->description_en;
                $model->meta_title = $productModel->meta_title;
                $model->meta_title_uk = $productModel->meta_title_uk;
                $model->meta_title_en = $productModel->meta_title_en;
                $model->meta_description = $productModel->meta_description;
                $model->meta_description_uk = $productModel->meta_description_uk;
                $model->meta_description_en = $productModel->meta_description_en;
                $model->save();
                $productID = $model->id;
                foreach ($productModel->optionTranslate as $option) {
                    $model = new ProductOption();
                    $model->product_id = $productID;
                    $model->is_filter = $option->is_filter;
                    $model->option = $option->option;
                    $model->option_en = $option->option_en;
                    $model->option_uk = $option->option_uk;
                    $model->value = $option->value;
                    $model->value_en = $option->value_en;
                    $model->value_uk = $option->value_uk;
                    $model->save();
                }
            }
        }
    }

}
