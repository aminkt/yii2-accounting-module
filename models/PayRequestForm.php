<?php

namespace userAccounting\models;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['amount', 'account'], 'required'],
            [['amount'], 'number', 'min'=>10000],
            [['account'], 'integer'],
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
        ];
    }


    public function regPayRequest(){
        if($this->validate()){
            $payRequest = new PayRequest();
            $payRequest->amount = $this->amount;
            $payRequest->accountId = $this->account;
            $payRequest->userId = Yii::$app->getUser()->getId();
            $payRequest->status = PayRequest::STATUS_WAITING;
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
