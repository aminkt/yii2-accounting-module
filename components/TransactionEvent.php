<?php
namespace userAccounting\components;

use yii\base\Event;

class TransactionEvent extends Event
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    /** @var  string $type Transaction type. */
    public $type;

    /** @var  double $amount Amount of transaction. */
    public $amount;

    /** @var  \aminkt\userAccounting\interfaces\PurseInterface $purse Purse object of transaction. */
    public $purse;

    /** @var  \yii\web\IdentityInterface $userId User object of transaction. */
    public $userId;

    /** @var  string $description Description of transaction. */
    public $description;

    /** @var  integer $time Time of transaction in unixTimeStamp */
    public $time;

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return self;
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return \aminkt\userAccounting\interfaces\PurseInterface
     */
    public function getPurse()
    {
        return $this->purse;
    }

    /**
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     *
     * @return self;
     */
    public function setPurse($purse)
    {
        $this->purse = $purse;
        return $this;
    }

    /**
     * @return integer|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param integer|string $user
     *
     * @return self
     */
    public function setUserId($user)
    {
        $this->userId = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self;
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     *
     * @return self;
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}