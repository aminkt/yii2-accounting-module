<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\TransactionInterface;
use aminkt\userAccounting\interfaces\UserInterface;

/**
 * This is the model class for table "{{%user_accounting_transactions}}".
 *
 * @property int $id
 * @property int $userId
 * @property int $purseId
 * @property double $amount
 * @property double $purseRemains
 * @property double $totalRemains
 * @property string $description
 * @property int $type
 * @property string $time
 *
 * @property Purse $purse
 * @property UserInterface $user
 */
class Transaction extends \yii\db\ActiveRecord implements TransactionInterface
{
    /**
     * Deposit amount to defined purse.
     *
     * @param double $amount
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     * @param string $description
     * @param integer $type
     *
     * @return boolean  True if deposit apply correctly and false if not.
     */
    public static function deposit($amount, $purse, $description, $type)
    {
        $transaction = self::create($amount, $purse, $description, $type);
        if ($transaction->save())
            return true;
        \Yii::error($transaction->getErrors(), self::className());
        return false;
    }

    /**
     * Create and initialize a new transaction.
     *
     * @param double $amount
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     * @param string $description
     * @param integer $type
     *
     * @return self
     */
    private static function create($amount, $purse, $description, $type)
    {
        $transaction = new Transaction();
        $transaction->userId = $purse->getUserId();
        $transaction->purseId = $purse->getId();
        $transaction->description = $description;
        $transaction->type = $type;
        $transaction->amount = $amount;

        $purseAmount = $purse->getAmount();
        $transaction->purseRemains = $purseAmount + $amount;

        $totalAmount = UserAccounting::getAmount($purse->getUserId());
        $transaction->totalRemains = $totalAmount + $amount;

        return $transaction;
    }

    /**
     * Withdraw amount to defined purse.
     *
     * @param double $amount
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     * @param string $description
     * @param integer $type
     *
     * @return boolean  True if withdraw apply correctly and false if not.
     */
    public static function withdraw($amount, $purse, $description, $type)
    {
        $transaction = self::create((-$amount), $purse, $description, $type);
        if ($transaction->save())
            return true;
        \Yii::error($transaction->getErrors(), self::className());
        return false;
    }

    /**
     * Calculate total deposit of a purse.
     *
     * @param PurseInterface|integer $purse
     *
     * @return double   This number is positive
     */
    public static function getTotalPurseDeposit($purse)
    {
        $amount = self::find()->where([
            'userId' => $purse->getUserId(),
            'purseId' => $purse->getId(),
        ])->andWhere(['>', 'amount', 0])->sum('amount');
        return abs(doubleval($amount));
    }

    /**
     * Calculate total withdraw of a purse.
     *
     * @param PurseInterface|integer $purse
     *
     * @return double   This number is positive.
     */
    public static function getTotalPurseWithdraw($purse)
    {
        $amount = self::find()->where([
            'userId' => $purse->getUserId(),
            'purseId' => $purse->getId(),
        ])->andWhere(['<', 'amount', 0])->sum('amount');
        return abs(doubleval($amount));
    }

    /**
     * @param integer|UserInterface $fromUser
     * @param integer|UserInterface $toUser
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_transactions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'purseId', 'type'], 'integer'],
            [['amount', 'purseRemains', 'totalRemains'], 'number'],
            [['description'], 'string'],
            [['time'], 'safe'],
            [['purseId'], 'exist', 'skipOnError' => true, 'targetClass' => Purse::className(), 'targetAttribute' => ['purseId' => 'id']],
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
            'purseId' => 'Purse ID',
            'amount' => 'Amount',
            'purseRemains' => 'Purse Remains',
            'totalRemains' => 'Total Remains',
            'description' => 'Description',
            'type' => 'Type',
            'time' => 'Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurse()
    {
        return $this->hasOne(Purse::className(), ['id' => 'purseId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\aminkt\userAccounting\UserAccounting::getInstance()->userModel, ['id', 'userId']);
    }

    /**
     * Return amount of transaction.
     *
     * @return double
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return remaining money of purse after creating current transaction.
     *
     * @return double
     */
    public function getPurseRemain()
    {
        return $this->purseRemains;
    }

    /**
     * Return total remaining money of all user purses.
     *
     * @return double
     */
    public function getTotalRemain()
    {
        return $this->totalRemains;
    }

    /**
     * Return transaction description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return type of transaction.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return transaction time.
     *
     * @return integer|string
     */
    public function getTime()
    {
        return \Yii::$app->getFormatter()->asTimestamp($this->time);
    }
}
