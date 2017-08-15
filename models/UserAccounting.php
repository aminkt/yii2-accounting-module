<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\interfaces\AccountingInterface;
use aminkt\userAccounting\interfaces\AccountInterface;
use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\SettlementRequestInterface;
use userAccounting\components\TransactionEvent;
use yii\validators\NumberValidator;

/**
 * This is the model class for table "{{%useraccounting}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $meta
 * @property string $value
 * @property integer $time
 */
class UserAccounting extends \yii\db\ActiveRecord implements AccountingInterface
{
    const TYPE_BALANCE = 1;
    const TYPE_INCOME = 2;
    const TYPE_COSTS = 3;

    const EVENT_DEPOSIT = 'deposit';
    const EVENT_WITHDRAWAL = 'withdrawal';

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_DEPOSIT, [$this, 'onDeposit']);
        $this->on(self::EVENT_WITHDRAWAL, [$this, 'onWithdrawal']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'type'], 'required'],
            [['userId', 'type'], 'integer'],
            ['type', 'in', 'range'=>[self::TYPE_BALANCE, self::TYPE_INCOME, self::TYPE_COSTS]],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => 'User ID',
            'type' => 'Type',
            'amount' => 'Amount',
        ];
    }

    /**
     * Deposit amount to user account.
     * @param integer $amount
     * @return bool|float
     */
    public function deposit($amount){
        $validator = new NumberValidator([
            'integerOnly'=>true,
            'min'=>0,
        ]);
        if($validator->validate($amount, $error)){
            $this->amount += $amount;
            if($this->save()){
                $event = new TransactionEvent();
                $event->amount = $amount;
                $event->time = time();
                $this->trigger(self::EVENT_DEPOSIT, $event);
                return $this->amount;
            }else{
                \Yii::error($this->getErrors(), self::className());
            }
        }else{
            \Yii::error($error, self::className());
        }
        return false;
    }

    /**
     * Withdrawal amount from user account.
     * @param integer $amount
     * @return bool|float
     */
    public function withdraw($amount)
    {
        $validator = new NumberValidator([
            'integerOnly'=>true,
            'min'=>0,
            'max'=>$this->amount
        ]);
        if($validator->validate($amount, $error)){
            $this->amount -= $amount;
            if($this->amount>0)
                if($this->save()){
                    $event = new TransactionEvent();
                    $event->amount = $amount;
                    $event->time = time();
                    $this->trigger(self::EVENT_WITHDRAWAL, $event);
                    return $this->amount;
                }else{
                    \Yii::error($this->getErrors(), self::className());
                }
            else
                \Yii::error("Cant withdrawal amount less than 0");
        }else{
            \Yii::error($error, self::className());
        }
        return false;
    }

    /**
     * Event handler invoked when deposit happened.
     * @param $event TransactionEvent
     */
    public function onDeposit($event){
        $account = UserAccounting::findOne([
            'userId'=>$this->userId,
            'type'=>UserAccounting::TYPE_INCOME
        ]);
        if(!$account){
            $account = new UserAccounting();
            $account->userId = $this->userId;
            $account->type = UserAccounting::TYPE_INCOME;
            $account->amount = 0;
            if($account->save(false))
                throw new \RuntimeException('Cant create UserAccount model');
        }

        $account->amount += $event->amount;
        $account->save(false);
    }

    /**
     * Event handler invoked when withdrawal happened.
     * @param $event TransactionEvent
     */
    public function onWithdrawal($event){
        $account = UserAccounting::findOne([
            'userId'=>$this->userId,
            'type'=>UserAccounting::TYPE_COSTS
        ]);
        if(!$account){
            $account = new UserAccounting();
            $account->userId = $this->userId;
            $account->type = UserAccounting::TYPE_COSTS;
            $account->amount = 0;
            if(!$account->save(false))
                throw new \RuntimeException('Cant create UserAccount model');
        }

        $account->amount += $event->amount;
        $account->save(false);
    }

    /**
     * Return key value.
     *
     * @param string $key
     *
     * @return string
     */
    public static function getValue($key, $userIdentity = null)
    {
        // TODO: Implement getValue() method.
    }

    /**
     * Return user amount.
     *
     * @param \yii\web\IdentityInterface $userIdentity
     *
     * @return float
     */
    public static function getAmount($userIdentity = null)
    {
        // TODO: Implement getAmount() method.
    }

    /**
     * Create a settlement request from selected purse to selected account.
     *
     * @param float $amount
     * @param PurseInterface $purse
     * @param AccountInterface $account
     * @param string|null $description
     * @param int $type
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return SettlementRequestInterface
     */
    public static function settlementRequest($amount, $purse, $account, $description = null, $type = SettlementRequestInterface::TYPE_SHABA, $userIdentity = null)
    {
        // TODO: Implement settlementRequest() method.
    }

    /**
     * Block a settlement request.
     *
     * @param SettlementRequestInterface $request
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function blockSettlementRequest($request, $note = null, $userIdentity = null)
    {
        // TODO: Implement blockSettlementRequest() method.
    }

    /**
     * Confirm a settlment request.
     *
     * @param SettlementRequestInterface $request
     * @param string $bankTrackingCode Bank trakcing code for loging.
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function confirmSettlementRequest($request, $bankTrackingCode, $note = null, $userIdentity = null)
    {
        // TODO: Implement confirmSettlementRequest() method.
    }

    /**
     * Reject a settlement request.
     *
     * @param SettlementRequestInterface $request
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function rejectSettlementRequest($request, $note = null, $userIdentity = null)
    {
        // TODO: Implement rejectSettlementRequest() method.
    }

    /**
     * Create a new purse.
     *
     * @param \yii\web\IdentityInterface $userIdentity Owner identity object.
     * @param string $name Name of purse
     * @param string|null $description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return PurseInterface
     */
    public static function createPurse($userIdentity, $name, $description = null)
    {
        // TODO: Implement createPurse() method.
    }

    /**
     * Block a purse.
     *
     * @param PurseInterface $purse
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function blockPurse($purse, $note = null, $userIdentity = null)
    {
        // TODO: Implement blockPurse() method.
    }

    /**
     * Unblock a blocked purse.
     *
     * @param PurseInterface $purse
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function unblockPurse($purse, $note = null, $userIdentity = null)
    {
        // TODO: Implement unblockPurse() method.
    }

    /**
     * Remove a purse.
     *
     * @param PurseInterface $purse
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function removePurse($purse, $force = false, $userIdentity = null)
    {
        // TODO: Implement removePurse() method.
    }

    /**
     * Create a bank account.
     *
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     * @param string|null $bankName Account bank name.
     * @param string|null $owner Account owner name
     * @param string|null $cardNumber Account card number
     * @param string|null $shaba Account Shaba number
     * @param string|null $accountNumber Account nummber
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return AccountInterface
     */
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null)
    {
        // TODO: Implement createAccount() method.
    }

    /**
     * Confirm an account.
     *
     * @param AccountInterface $account
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function confirmAccount($account, $note = null, $userIdentity = null)
    {
        // TODO: Implement confirmAccount() method.
    }

    /**
     * Block an account.
     *
     * @param AccountInterface $account
     * @param string|null $note
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public static function blockAccount($account, $note = null, $userIdentity = null)
    {
        // TODO: Implement blockAccount() method.
    }

    /**
     * Remove an account.
     *
     * @param AccountInterface $account
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     *
     * @return void
     */
    public static function removeAccount($account, $force = false, $userIdentity = null)
    {
        // TODO: Implement removeAccount() method.
    }

    /**
     * Return a accounting model.
     *
     * @param string $meta
     * @param integer|\yii\web\IdentityInterface $user
     * @return mixed
     */
    public static function get($meta, $user)
    {
        // TODO: Implement get() method.
    }
}
