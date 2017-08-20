<?php
namespace userAccounting\components;


use aminkt\userAccounting\models\Transaction;
use yii\base\Event;

class TransactionEvent extends Event
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    /** @var  Transaction $transaction */
    public $transaction;
    /** @var  string    Transaction type */
    public $type;

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param Transaction $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
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
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}