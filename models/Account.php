<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\AccountInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_accounting_accounts}}".
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
 * @property string $operatorNote
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property Settlement[] $settlements
 */
class Account extends ActiveRecord implements AccountInterface
{
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
            [['operatorNote'], 'string'],
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
    public function getSettlements()
    {
        return $this->hasMany(Settlement::className(), ['accountId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null)
    {
        $account = new static();
        $account->userId = $userIdentity->getId();
        $account->bankName = $bankName;
        $account->owner = $owner;
        $account->cardNumber = $cardNumber;
        $account->shaba = $shaba;
        $account->accountNumber = $accountNumber;
        $account->status = self::STATUS_WAITING;
        if ($account->save())
            return $account;

        \Yii::error($account->getErrors(), self::class);
        throw new RuntimeException("Account model creation become failed");
    }

    /**
     * @inheritdoc
     */
    public function confirmAccount($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_CONFIRMED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account confirmation become failed");
        }
    }

    /**
     * @inheritdoc
     */
    public function blockAccount($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_BLOCKED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account blocking become failed");
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAccount()
    {
        $this->status = self::STATUS_REMOVED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account removing become failed");
        }
    }
}
