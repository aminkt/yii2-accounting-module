<?php

namespace aminkt\userAccounting;
use aminkt\userAccounting\controllers\panel\DefaultController;
use aminkt\userAccounting\exceptions\InvalidArgumentException;

/**
 * userAccounting module definition class
 *
 * @property \aminkt\userAccounting\components\Account $account    Account component.
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 * @package aminkt\userAccounting
 */
class UserAccounting extends \yii\base\Module
{
    const EVENT_PURSE_DEPOSIT = 'userAccounting.purse.deposit';
    const EVENT_PURSE_WITHDRAW = 'userAccounting.purse.withdraw';
    const EVENT_PURSE_DEPOSIT_ACK = 'userAccounting.purse.deposit.ack';
    const EVENT_PURSE_WITHDRAW_ACK = 'userAccounting.purse.withdraw.ack';

    const ADMIN_CONTROLLER_NAMESPACE = 'aminkt\userAccounting\controllers\admin';
    const PANEL_CONTROLLER_NAMESPACE = 'aminkt\userAccounting\controllers\panel';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\userAccounting\controllers\panel';

    /** @var string|null $transactionModel Transaction model class name. */
    public $transactionModel = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->transactionModel)
            throw new InvalidArgumentException("Transaction model not defined");

        \Yii::configure($this, require( __DIR__.DIRECTORY_SEPARATOR.'config.php'));
        $this->controllerMap = [
            'account'=>DefaultController::className(),
        ];
    }

    public static function getInstance()
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('userAccounting');
    }
}
