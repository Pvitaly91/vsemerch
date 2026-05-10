<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $phone
 * @property string $address
 * @property string $email
 * @property string $notes
 * @property string $status
 *
 * @property OrderItem[] $orderItems
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 'New';
    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_DONE = 'Done';
    public $npCity;
    public $npDep;
    public $CityGid;
    public $DepGid;
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['phone', 'email', 'name'], 'required'],
            [['notes', 'address','npCity','npDep','CityGid','DepGid'], 'string'],
            [['phone', 'email', 'name','status'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['payment'], 'string'],
        ];
       
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'created_at' => Yii::t('shop', 'Created At'),
            'updated_at' => Yii::t('shop', 'Updated At'),
            'name' => Yii::t('shop', 'FIO'),
            'phone' => Yii::t('shop', 'Phone'),
            'address' => Yii::t('shop', 'Address'),
            'email' => Yii::t('shop', 'Email'),
            'notes' => Yii::t('shop', 'Notes'),
            'status' => Yii::t('shop', 'Status'),
            'total' => Yii::t('shop', 'Total'),
            'npCity' => Yii::t('shop', 'npCity'),
            'npDep' => Yii::t('shop', 'npDep'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }

    public function getTotal()
    {
        return round($this->hasOne(OrderItem::className(), ['order_id' => 'id'])->sum('quantity*price'), 2);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->status = self::STATUS_NEW;
            }
            return true;
        } else {
            return false;
        }
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_DONE => Yii::t('shop', 'Done'),
            self::STATUS_IN_PROGRESS => Yii::t('shop', 'In progress'),
            self::STATUS_NEW => Yii::t('shop', 'New'),
        ];
    }

    public function sendEmail()
    {
        return Yii::$app->mailer->compose('order', ['order' => $this])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setFrom('no-reply@vsemerch.com.ua')
            ->setSubject('Нове замовлення #' . $this->id)
            ->send();
    }
}
