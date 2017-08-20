<?php

namespace aminkt\userAccounting\models;

use userAccounting\components\Account;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class PayRequestForm extends Model
{
    public $amount;
    public $account;
    public $purse;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['amount', 'account',], 'required'],
            [['amount'], 'number', 'min'=>10000],
            [['account', 'purse'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => 'مبلغ (تومان)',
            'account' => 'حساب',
            'purse' => 'کیف پول',
        ];
    }


    /**
     * Create a settlement request. request.
     *
     * @deprecated Since ver 1.0 you should use `Settlement()` instead this method. available until version 3.
     * @return bool
     */
    public function regPayRequest(){
        if($this->validate()){
            $payRequest = new Settlement();
            $payRequest->amount = $this->amount;
            $payRequest->accountId = $this->account;
            $payRequest->userId = Yii::$app->getUser()->getId();
            $payRequest->status = Settlement::STATUS_WAITING;
            if($payRequest->save()){
                Account::withdrawal($this->amount, Transaction::TYPE_PAY_REQUEST, 'درخواست تسویه حساب');
                return true;
            }
            else{
                Yii::error($payRequest->getErrors(), self::className());
            }
        }
        return false;
    }
}
