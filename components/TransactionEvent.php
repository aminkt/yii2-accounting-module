<?php
namespace userAccounting\components;


use yii\base\Event;

class TransactionEvent extends Event
{
    /** @var  integer $amount */
    public $amount;
    /** @var  integer $time unixTimestamp */
    public $time;
}