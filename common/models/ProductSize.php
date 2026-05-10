<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;

/**
 * This is the model class for table "shop_product_size".
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $size_id
 * @property float|null $price
 *
 * @property Product $product
 * @property Size $size
 */
class ProductSize extends ActiveRecord implements CartPositionInterface
{
    use traits\minCountOrder,  CartPositionTrait, \common\models\traits\TotobiDiscount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_product_size';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'size_id'], 'integer'],
            [['price'], 'number'],
            [['code'], 'string'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['size_id'], 'exist', 'skipOnError' => true, 'targetClass' => Size::className(), 'targetAttribute' => ['size_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'size_id' => 'Size ID',
            'price' => Yii::t('shop', 'Price'),
            'code' => Yii::t('shop', 'Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSize()
    {
        return $this->hasOne(Size::className(), ['id' => 'size_id']);
    }

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
}
