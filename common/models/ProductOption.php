<?php

namespace common\models;

use common\components\MultilingualQuery;
use navatech\language\Translate;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Class Option
 * @package common\models
 */
class ProductOption extends ActiveRecord
{

    public $count_product;

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Language::getLanguagesAsArray(),
                'defaultLanguage' => 'ru',
                'langForeignKey' => 'shop_product_option_id',
                'tableName' => ProductOptionTranslate::tableName(),
                'attributes' => [
                    'option',
                    'value',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_option';
    }

    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        $this->slug_option = Inflector::slug($this->option_ru);
        $this->slug = Inflector::slug($this->value_ru);
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'is_filter'], 'integer'],
            [['option','value'], 'required'],
            [['option','option_uk'], 'filter', 'filter' => 'trim'],
            [['value','value_uk'], 'filter', 'filter' => 'trim'],
            [['option','option_uk'], 'string', 'max' => 255],
            [['value', 'value_uk'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = [
            'is_filter' => Yii::t('shop', 'Is Filter'),
            'option' => Yii::t('shop', 'Option'),
            'value' => Yii::t('shop', 'Value'),
            'option_uk' => Yii::t('shop', 'Option_uk'),
            'value_uk' => Yii::t('shop', 'Value_uk'),
            'option_en' => Yii::t('shop', 'Option_en'),
            'value_en' => Yii::t('shop', 'Value_en'),
        ];
        return $attributeLabels;
    }

    public function getProductOption()
    {
        return $this->hasMany(ProductOption::className(), ['product_id' => 'product_id']);
    }

    static public function recursiveRelation(&$query, $group_slug, $filters_array)
    {
        foreach ($filters_array as $group => $filter) {
            if ($group != $group_slug) {
                return $query->joinWith(['productOption' => function ($q) use ($filter, $group, $group_slug, $filters_array) {
                    $q->alias("po_{$group}")
                        ->andOnCondition(['IN', "`po_{$group}`.slug", $filter])
                        ->andOnCondition(["`po_{$group}`.slug_option" => $group]);
                    unset($filters_array[$group]);
                    return static::recursiveRelation($q, $group_slug, $filters_array);
                }]);
            }
        }
    }

    static public function getFilters($productsQuery, $filters_array)
    {
        $out = [];
        $groups = ProductOption::find()
            ->leftJoin($productsQuery->tablesUsedInFrom, $productsQuery->tablesUsedInFrom['{{shop_product}}'] . '.id = ' . ProductOption::tableName() . '.product_id')
            ->where($productsQuery->where)
            ->andWhere(['is_filter' => 1])
            ->groupBy('slug_option')
            ->orderBy('slug_option ASC')
            ->all();
        $i = 0;
        foreach ($groups as $_group) {
            $out[$i]['parent'] = $_group->option;
            $group_slug = $_group->slug_option;
            $query = ProductOption::find()->alias('po');
            $alias = "`po`";
            if (!empty($filters_array) && count($filters_array) > 0) {
                foreach ($filters_array as $group => $filter) {
                    if ($group != $group_slug) {
                        $query->joinWith(['productOption' => function ($q) use ($filter, $group, $filters_array) {
                            $q->andOnCondition(['IN', "`po_{$group}`.slug", $filter])
                                ->andOnCondition(["`po_{$group}`.slug_option" => $group])
                                ->andOnCondition(["`po_{$group}`.is_filter" => 1])
                                ->alias("po_{$group}");
                        }]);
                        $query->prepare(\Yii::$app->db->queryBuilder);
                        $query->alias("po_{$group}");
                        $alias = "`po_{$group}`";
                    }
                }

                $query->alias("po");
                if (!empty($query->join)) {
                    $query->join = array_reverse($query->join);
                }

            }

            $query->select(['po.*', "count({$alias}.product_id) as count_product"])
                ->join('JOIN', $productsQuery->tablesUsedInFrom, $productsQuery->tablesUsedInFrom['{{shop_product}}'] . '.id = po.product_id')
                ->andWhere($productsQuery->where)
                ->andWhere(['po.is_filter' => 1])
                ->andWhere(['po.slug_option' => $_group['slug_option']])
                ->groupBy('po.slug');


            $out[$i]['filters'] = $query->all();
            $i++;
        }

        return $out;
    }
}
