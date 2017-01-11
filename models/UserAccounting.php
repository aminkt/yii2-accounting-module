<?php

namespace userAccounting\models;
use userAccounting\components\TransactionEvent;
use yii\validators\NumberValidator;

/**
 * This is the model class for table "{{%useraccounting}}".
 *
 * @property integer $userId
 * @property integer $type
 * @property double $amount
 */
class UserAccounting extends \yii\db\ActiveRecord
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
        return '{{%useraccounting}}';
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
    public function withdrawal($amount){
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
}
