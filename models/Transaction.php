<?php

namespace userAccounting\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%useraccounting_transactions}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property double $amount
 * @property string $description
 * @property integer $type
 * @property integer $time
 */
class Transaction extends \yii\db\ActiveRecord
{
    const TYPE_UNKNOWN = 0;
    const TYPE_SALE = 1;
    const TYPE_GIFT = 2;
    const TYPE_PAY_REQUEST = 3;
    const TYPE_REJECT_PAY_REQUEST = 4;
    const TYPE_CHARGE_ACCOUNT = 5;
    const TYPE_BUY = 6;



    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['time', 'time'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                // 'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%useraccounting_transactions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'type', 'amount'], 'required'],
            [['userId', 'type', 'time'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'amount' => 'مبلغ (تومان)',
            'description' => 'توضیحات',
            'type' => 'نوع تراکنش',
            'time' => 'زمان تراکنش',
        ];
    }
}
