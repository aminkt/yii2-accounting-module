<?php

namespace aminkt\userAccounting\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%useraccounting_accounts}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $bankName
 * @property string $cardNumber
 * @property string $accountNumber
 * @property string $shaba
 * @property string $owner
 * @property double $amountPaid
 * @property integer $status
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property PayRequest[] $payRequests
 */
class Account extends ActiveRecord
{
    const STATUS_WAITING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_BLOCKED = 4;
    const STATUS_DEACTIVATE = 5;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_accounts}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createTime', 'updateTime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updateTime'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                // 'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'status', 'bankName', 'cardNumber', 'accountNumber', 'shaba', 'owner'], 'required'],
            [['userId', 'status', 'updateTime', 'createTime'], 'integer'],
            [['amountPaid'], 'number'],
            [['amountPaid'], 'default', 'value'=>0],
            [['bankName', 'cardNumber', 'accountNumber', 'shaba', 'owner'], 'string', 'max' => 255],
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
            'bankName' => 'نام بانک',
            'cardNumber' => 'شماره کارت',
            'accountNumber' => 'شماره حساب',
            'shaba' => 'شبا',
            'owner' => 'صاحب حساب',
            'amountPaid' => 'مبلغ واریز شده (تومان)',
            'status' => 'وضعیت',
            'updateTime' => 'Update Time',
            'createTime' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayRequests()
    {
        return $this->hasMany(PayRequest::className(), ['accountId' => 'id']);
    }
}
