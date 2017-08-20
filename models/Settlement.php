<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\SettlementRequestInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_accounting_settlements}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $purseId
 * @property integer $accountId
 * @property double $amount
 * @property string $description
 * @property string $operatorNote
 * @property integer $settlementType
 * @property string $bankTrackingCode
 * @property integer $status
 * @property integer $settlementTime
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property Account $account
 * @property Purse $purse
 * @property string $accountName
 */
class Settlement extends \yii\db\ActiveRecord implements SettlementRequestInterface
{
    const STATUS_WAITING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REJECTED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_settlements}}';
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
            [['userId', 'accountId', 'status', 'settlementType', 'payTime', 'updateTime', 'createTime'], 'integer'],
            [['operatorNote'], 'string'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getPurse()
    {
        return $this->hasOne(Purse::className(), ['id' => 'purseId']);
    }

    /**
     * Return account name
     * @return string
     */
    public function getAccountName(){
        $account = $this->account;
        return $account->bankName.'-'.$account->cardNumber;
    }

    /**
     * @inheritdoc
     */
    public static function createSettlementRequest($amount, $purse, $account, $description = null, $type = Settlement::TYPE_SHABA)
    {
        $settlement = new Settlement();
        $settlement->amount = $amount;
        $settlement->userId = $purse->userId;
        $settlement->purseId = $purse->id;
        $settlement->accountId = $account->id;
        $settlement->description = $description;
        $settlement->settlementType = $type;
        $settlement->status = self::STATUS_WAITING;
        if ($settlement->save())
            return $settlement;

        \Yii::error($settlement->getErrors(), self::className());
        throw new RuntimeException("Settlement has error in creation action.");
    }

    /**
     * Block a settlement request.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function blockSettlementRequest($note = null)
    {
        $this->status = self::STATUS_BLOCKED;
        $this->operatorNote = $note;

        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Settlement blocking become failed");
        }
    }

    /**
     * Confirm a settlment request.
     *
     * @param string $bankTrackingCode Bank trakcing code for loging.
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function confirmSettlementRequest($bankTrackingCode, $note = null)
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->operatorNote = $note;

        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Settlement confirmation become failed");
        }
    }

    /**
     * Reject a settlement request.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function rejectSettlementRequest($note = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->operatorNote = $note;

        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Settlement rejection become failed");
        }
    }
}
