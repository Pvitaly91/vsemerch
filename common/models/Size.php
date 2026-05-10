<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "shop_size".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property ProductSize[] $shopProductSizes
 */
class Size extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_size';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'slugAttribute' => 'slug'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('shop', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopProductSizes()
    {
        return $this->hasMany(ProductSize::className(), ['size_id' => 'id']);
    }

    public function sizeProduct($product_id = null)
    {
        if($product_id) {
            $query = ProductSize::find()->where(['size_id' => $this->id]);
            $query->andWhere(['product_id' => $product_id]);
        }
        if($product_id && $result = $query->one()) {
            return $result;
        } else {
            return new ProductSize();
        }
    }
}
