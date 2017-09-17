<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\SettlementRequestInterface;
use aminkt\userAccounting\interfaces\TransactionInterface;
use aminkt\userAccounting\UserAccounting;
use aminkt\userAccounting\components\SettlementEvent;
use Yii;
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
            [['userId', 'accountId', 'status', 'settlementType', 'settlementTime', 'updateTime', 'createTime'], 'integer'],
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
            'amount' => 'مبلغ (تومان)',
            'bankTrackingCode' => 'کدپیگیری تراکنش',
            'status' => 'وضعیت',
            'settlementTime' => 'زمان تراکنش',
            'updateTime' => 'آخرین زمان ویرایش',
            'createTime' => 'زمان درخواست',
            'settlementType' => 'شکل تسویه حساب',
            'operatorNote'=>'یادداشت اپراتور',
            'description' => 'توضیحات درخواست تسویه',
            'account' => 'حساب',
            'purse' => 'کیف پول',
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
        if (!is_double($amount) and !is_float($amount) and !is_integer($amount))
            throw new InvalidArgumentException("Amount is not valid.");

        $max = UserAccounting::getInstance()->maxSettlementAmount;
        $min = UserAccounting::getInstance()->minSettlementAmount;
        if ($amount <= 0 or $amount < $min)
            throw new InvalidArgumentException("Amount is so small.");

        if ($max and $amount > $max)
            throw new InvalidArgumentException("Amount is so huge.");

        $accountId = is_integer($account) ? $account : $account->getId();

        $settlement = new Settlement();
        $settlement->amount = $amount;
        $settlement->userId = $purse->getUserId();
        $settlement->purseId = $purse->getId();
        $settlement->accountId = $accountId;
        $settlement->description = $description;
        $settlement->settlementType = $type;
        $settlement->status = self::STATUS_WAITING;
        if ($settlement->save()) {
            $event = new SettlementEvent();
            $event->setSettlement($settlement);
            Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_SETTLEMENT_CREATE, $event);

            /** @var TransactionInterface $transactionModel */
            $transactionModel = UserAccounting::getInstance()->transactionModel;
            $transactionModel::withdraw($amount, $purse, 'برداشت بابت تسویه حساب', TransactionInterface::TYPE_SETTLEMENT_REQUEST_WITHDRAW);

            return $settlement;
        }

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

        $event = new SettlementEvent();
        $event->setSettlement($this);
        Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_SETTLEMENT_BLOCK, $event);
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

        $event = new SettlementEvent();
        $event->setSettlement($this);
        Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_SETTLEMENT_CONFIRM, $event);
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
        $this->status = self::STATUS_REJECTTED;
        $this->operatorNote = $note;

        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Settlement rejection become failed");
        }

        /** @var TransactionInterface $transactionModel */
        $transactionModel = UserAccounting::getInstance()->transactionModel;
        $transactionModel::deposit($this->amount, $this->purse, 'واریز بابت رد درخواست تسویه حساب', TransactionInterface::TYPE_SETTLEMENT_REQUEST_REJECTED);

        $event = new SettlementEvent();
        $event->setSettlement($this);
        Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_SETTLEMENT_REJECT, $event);
    }

    /**
     * @param integer $fromUser
     * @param integer $toUser
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return bool
     */
    public static function migrate($fromUser, $toUser)
    {
        $q = new \yii\db\Query();
        $q->createCommand()->update(self::tableName(), ['userId' => $toUser], ['userId' => $fromUser])->execute();
        return true;
    }

    /**
     * @return array
     */
    public static function getSettlementTypeList()
    {
        return [
            self::TYPE_CART_TO_CART => 'کارت به کارت',
            self::TYPE_SHABA => 'شماره شبا'
        ];
    }

    /**
     * @return string
     */
    public function getSettlementTypeLabel()
    {
        return self::getSettlementTypeList()[$this->settlementType];
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_WAITING => 'در انتظار تائید',
            self::STATUS_CONFIRMED => 'تائید شده',
            self::STATUS_REJECTTED => 'عدم احراز صلاحیت',
            self::STATUS_BLOCKED => 'مسدود شده',
            self::STATUS_REMOVED => 'حذف شده'
        ];
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        return self::getStatusList()[$this->status];
    }
}
