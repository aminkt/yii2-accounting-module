<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\TransactionInterface;
use Yii;

/**
 * This is the model class for table "{{%user_accounting_purses}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $accountId
 * @property string $name
 * @property string $description
 * @property string $operatorNote
 * @property double $totalDeposit
 * @property double $totalWithdraw
 * @property integer $autoSettlement
 * @property integer $status
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property Account $account
 */
class Purse extends \yii\db\ActiveRecord implements PurseInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_purses}}';
    }

    /**
     * Create a new purse.
     *
     * @param \yii\web\IdentityInterface $userIdentity Owner identity object.
     * @param string $name Name of purse
     * @param string|null $description
     *
     * @throws \aminkt\userAccounting\exceptions\\RuntimeException Throw if process stop unexpectly.
     *
     * @return PurseInterface
     */
    public static function createPurse($userIdentity, $name, $description = null)
    {
        $purse = new Purse();
        $purse->userId = $userIdentity->getId();
        $purse->name = $name;
        $purse->description = $description;
        $purse->status = self::STATUS_CONFIRMED;
        if ($purse->save())
            return $purse;

        \Yii::error($purse->getErrors(), self::class);
        throw new RuntimeException("Purse model creation become failed");
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'accountId', 'autoSettlement', 'status', 'updateTime', 'createTime'], 'integer'],
            [['name'], 'required'],
            [['description', 'operatorNote'], 'string'],
            [['totalDeposit', 'totalWhitdraw'], 'number'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'description' => 'Description',
            'operatorNote' => 'Operator Note',
            'totalDeposit' => 'Total Deposit',
            'totalWhitdraw' => 'Total Whitdraw',
            'autoSettlement' => 'Auto Settlement',
            'status' => 'Status',
            'updateTime' => 'Update Time',
            'createTime' => 'Create Time',
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
     * @inheritdoc
     */
    public function deposit($amount, $description = null)
    {
        if (!(is_integer($amount) or is_float($amount) or is_double($amount))) {
            throw new InvalidArgumentException("Amount should be in double.");
        }
        $transactionModel = new Transaction();
        $transaction = Transaction::getDb()->beginTransaction();
        try {
            $transactionModel->amount = $amount;
            $transactionModel->purseId = $this->id;
            $transactionModel->purseRemains = $this->getAmount() + $amount;
            $transactionModel->totalRemains = (UserAccounting::getAmount($this->id) + $amount);
            $transactionModel->description = $description;
            $transactionModel->type = Transaction::TYPE_NORMAL;
            $transactionModel->save();

            $this->totalDeposit += $amount;
            if (!$this->save())
                throw new RuntimeException("Purse cant update itself in deposit action.");

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Return amount of purse.
     *
     * @return double
     */
    public function getAmount()
    {
        return $this->getTotalDeposit() - $this->getTotalWithdraw();
    }

    /**
     * @return float
     */
    public function getTotalDeposit()
    {
        return $this->totalDeposit;
    }

    /**
     * @return float
     */
    public function getTotalWithdraw()
    {
        return $this->totalWithdraw;
    }

    /**
     * @inheritdoc
     */
    public function withdraw($amount, $description = null)
    {
        if (!(is_integer($amount) or is_float($amount) or is_double($amount))) {
            throw new InvalidArgumentException("Amount should be in double.");
        }
        $transactionModel = new Transaction();
        $transaction = Transaction::getDb()->beginTransaction();
        try {
            $transactionModel->amount = (-$amount);
            $transactionModel->purseId = $this->id;
            $transactionModel->purseRemains = $this->getAmount() - $amount;
            $transactionModel->totalRemains = (UserAccounting::getAmount($this->id) - $amount);
            $transactionModel->description = $description;
            $transactionModel->type = Transaction::TYPE_NORMAL;
            $transactionModel->save();

            $this->totalWithdraw += $amount;
            if (!$this->save())
                throw new RuntimeException("Purse cant update itself in withdraw action.");

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Block a purse.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function blockPurse($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_BLOCKED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Purse blocking become failed");
        }
    }

    /**
     * Unblock a blocked purse.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function unblockPurse($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_CONFIRMED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Purse unblocking become failed");
        }
    }

    /**
     * Remove a purse.
     *
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function removePurse($force = false)
    {
        $this->status = self::STATUS_CONFIRMED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Purse unblocking become failed");
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getOperatorNote()
    {
        return $this->operatorNote;
    }

    /**
     * @return bool
     */
    public function isAutoSettlement()
    {
        if ($this->autoSettlement)
            return true;
        return false;
    }
}
