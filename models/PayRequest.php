<?php

namespace userAccounting\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%useraccounting_pay_requests}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $accountId
 * @property double $amount
 * @property string $bankTrackingCode
 * @property integer $status
 * @property integer $payTime
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property Account $account
 * @property string $accountName
 */
class PayRequest extends \yii\db\ActiveRecord
{
    const STATUS_WAITING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REJECTED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%useraccounting_pay_requests}}';
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
            [['userId', 'accountId', 'status', 'amount'], 'required'],
            [['userId', 'accountId', 'status', 'payTime', 'updateTime', 'createTime'], 'integer'],
            [['amount'], 'number'],
            [['bankTrackingCode'], 'string', 'max' => 255],
            [['accountId'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['accountId' => 'id']],
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
            'accountId' => 'Account ID',
            'accountName' => 'حساب',
            'amount' => 'مبلغ',
            'bankTrackingCode' => 'کدپیگیری تراکنش',
            'status' => 'وضعیت',
            'payTime' => 'زمان تراکنش',
            'updateTime' => 'Update Time',
            'createTime' => 'زمان درخواست',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'accountId']);
    }

    /**
     * Return account name
     * @return string
     */
    public function getAccountName(){
        $account = $this->account;
        return $account->bankName.'-'.$account->cardNumber;
    }
}
