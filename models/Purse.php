<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\TransactionInterface;
use userAccounting\components\TransactionEvent;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%user_accounting_purses}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $accountId
 * @property string $name
 * @property string $description
 * @property string $operatorNote
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


    public function init()
    {
        parent::init();
        $this->on(\aminkt\userAccounting\UserAccounting::EVENT_PURSE_DEPOSIT_ACK, [$this, 'onDeposit']);
        $this->on(\aminkt\userAccounting\UserAccounting::EVENT_PURSE_WITHDRAW_ACK, [$this, 'onWithdraw']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
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
        try {
            $event = new TransactionEvent();
            $event->setAmount($amount)
                ->setPurse($this)
                ->setUserId($this->userId)
                ->setDescription($description)
                ->setType($event::TYPE_DEPOSIT)
                ->setTime(time());
            Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_PURSE_DEPOSIT, $event);

            /** @var TransactionInterface $transactionModelName */
            $transactionModelName = \aminkt\userAccounting\UserAccounting::getInstance()->transactionModel;
            $transactionModelName::deposit($amount, $this, $description, TransactionInterface::TYPE_NORMAL);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function withdraw($amount, $description = null)
    {
        if (!(is_integer($amount) or is_float($amount) or is_double($amount))) {
            throw new InvalidArgumentException("Amount should be in double.");
        }
        try {
            $event = new TransactionEvent();
            $event->setAmount($amount)
                ->setPurse($this)
                ->setUserId($this->userId)
                ->setDescription($description)
                ->setType($event::TYPE_WITHDRAW)
                ->setTime(time());
            Yii::$app->trigger(\aminkt\userAccounting\UserAccounting::EVENT_PURSE_DEPOSIT, $event);

            /** @var TransactionInterface $transactionModelName */
            $transactionModelName = \aminkt\userAccounting\UserAccounting::getInstance()->transactionModel;
            $transactionModelName::withdraw($amount, $this, $description, TransactionInterface::TYPE_NORMAL);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Event handler of on deposit action.
     *
     * @param TransactionEvent $event
     *
     * @return void
     */
    public function onDeposit(TransactionEvent $event)
    {

    }

    /**
     * Event handler of on withdrawal action.
     *
     * @param TransactionEvent $event
     *
     * @return void
     */
    public function onWithdraw(TransactionEvent $event)
    {

    }

    /**
     * Calculate and return amount of purse.
     *
     * @return double
     */
    public function getAmount()
    {
        /** @var \aminkt\userAccounting\interfaces\TransactionInterface $transactionModelName */
        $transactionModelName = \aminkt\userAccounting\UserAccounting::getInstance()->transactionModel;
        return $transactionModelName::getTotalPurseDeposit($this) - $transactionModelName::getTotalPurseWithdraw($this);
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
        $q = new Query();
        $fromPurses = Purse::findAll(['userId' => $fromUser]);
        foreach ($fromPurses as $purse) {
            $same = Purse::findOne([
                'userId' => $toUser,
                'name' => $purse->name
            ]);
            if ($same) {
                $purse->name .= '- همگام شده';
                $purse->description = 'این کیف پول از حساب قبلی شما همگام شده است. در صورت تمایل آن را حذف کنید.';
                $purse->save(false);
            }
        }
        $q->createCommand()->update(self::tableName(), ['userId' => $toUser], ['userId' => $fromUser])->execute();
        return true;
    }

    /**
     * Return purse user object
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
