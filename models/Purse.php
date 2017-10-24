<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\components\TransactionEvent;
use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RiskException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\AccountInterface;
use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\TransactionInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 * @property \aminkt\userAccounting\interfaces\UserInterface $user
 */
class Purse extends \yii\db\ActiveRecord implements PurseInterface
{
    protected $user;

    /**
     * Return user model.
     *
     * @return \aminkt\userAccounting\interfaces\UserInterface
     */
    public function getUser()
    {
        if (!$this->user) {
            /** @var \aminkt\userAccounting\interfaces\UserInterface $userModel */
            $userModel = \aminkt\userAccounting\UserAccounting::getInstance()->userModel;
            $this->user = $userModel::findOne($this->userId);
        }

        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_purses}}';
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
     * @param \aminkt\userAccounting\interfaces\UserInterface $userIdentity Owner identity object.
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
    public function edit($name = null, $description = null)
    {
        if ($name)
            $this->name = $name;

        if ($description)
            $this->description = $description;

        if ($this->save())
            return $this;

        Yii::error($this->getErrors(), self::className());
        throw new RuntimeException("Purse edit process become failed.");
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
            'userId' => 'شناسه کاربر',
            'accountId' => 'حساب بانکی متصل',
            'name' => 'نام',
            'description' => 'توضیحات',
            'operatorNote' => 'یاداشت اپراتور',
            'autoSettlement' => 'تسویه خودکار',
            'status' => 'وضعیت',
            'updateTime' => 'تاریخ ویرایش',
            'createTime' => 'تاریخ ایجاد',
        ];
    }

    /**
     * Delete purse object by changing status to removed.
     *
     * @throws RiskException
     *
     * @return bool
     */
    public function delete()
    {
        if ($this->getAmount() > 0) {
            throw new RiskException("Purse is not empty so you can not delete this purse.");
        }

        if ($this->beforeDelete()) {
            $this->status = self::STATUS_REMOVED;
            if ($this->save(false)) {
                $this->afterDelete();
                return true;
            }
            Yii::error($this->getErrors(), self::className());
            throw new \RuntimeException("Can not delete purse.");
        }
        return false;
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

            if ($this->autoSettlement and $this->accountId) {
                $settlementAmount = $this->getAmount();
                try {
                    Settlement::createSettlementRequest($settlementAmount, $this, $this->accountId, 'تسویه حساب خودکار');
                } catch (InvalidArgumentException $exception) {
                    Yii::warning("Settlement request amount is not valid. Amount was $settlementAmount", self::className());
                }
            }
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
                /** @var \aminkt\userAccounting\interfaces\TransactionInterface $model */
                $model = \aminkt\userAccounting\UserAccounting::getInstance()->transactionModel;
                $q->createCommand()->update($model::tableName(), ['userId' => $toUser, 'purseId' => $same->id], ['userId' => $fromUser, 'purseId' => $purse->id])->execute();
                $purse->delete();
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

    /**
     * Find purse by userId.
     * @param integer $userId
     * @return static[]
     */
    public static function findByUserId($userId)
    {
        return static::findAll(['userId' => $userId, 'status' => self::STATUS_CONFIRMED]);
    }

    /**
     * @inheritdoc
     */
    public function setAccount($account, $autoSettlement = true)
    {
        if ($account instanceof AccountInterface) {
            $accountId = $account->getId();
        } else {
            $accountId = $account;
        }

        $this->accountId = $accountId;
        if ($autoSettlement) {
            $this->autoSettlement = self::AUTO_SETTLEMENT_ON;
        } else {
            $this->autoSettlement = self::AUTO_SETTLEMENT_OFF;
        }

        if ($this->save()) {
            return $this;
        }

        \Yii::error($this->getErrors(), self::className());
        throw new RuntimeException("Can not assign account to current purse");
    }

    /**
     * @inheritdoc
     */
    public function removeAccount()
    {
        $this->accountId = null;
        $this->autoSettlement = self::AUTO_SETTLEMENT_OFF;
        if ($this->save()) {
            return $this;
        }

        \Yii::error($this->getErrors(), self::className());
        throw new RuntimeException("Can not remove account from current purse");
    }

    /**
     * @inheritdoc
     */
    public function autoSettlementOn()
    {
        if ($this->autoSettlement == self::AUTO_SETTLEMENT_ON)
            return true;

        if ($this->accountId) {
            $this->autoSettlement = self::AUTO_SETTLEMENT_ON;
            $this->save(false);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function autoSettlementOff()
    {
        if ($this->autoSettlement == self::AUTO_SETTLEMENT_OFF)
            return;

        $this->autoSettlement = self::AUTO_SETTLEMENT_OFF;
        $this->save(false);
    }
}
