<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\RuntimeException;
use userAccounting\components\Account;
use yii\base\Model;

/**
 * Login form
 */
class SettlementRequestForm extends Model
{
    /** @var  double $amount */
    public $amount;
    /** @var  integer $account Account id */
    public $account;
    /** @var  integer $purse Purse id */
    public $purse;
    /** @var  string $description Settlement req description */
    public $description;
    /** @var  integer $type Settlement type. */
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['amount', 'account',], 'required'],
            [['amount'], 'number', 'min'=>10000],
            [['account', 'purse', 'type'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => 'مبلغ (تومان)',
            'description' => 'توضیحات درخواست تسویه',
            'type' => 'شکل تسویه حساب',
            'account' => 'حساب',
            'purse' => 'کیف پول',
        ];
    }

    /**
     * Create a settlement request. request.
     *
     * @return bool
     */
    public function settlement()
    {
        if($this->validate()){
            try {
                $settlement = UserAccounting::settlementRequest($this->amount, $this->purse, $this->account, $this->description, $this->type);
                return true;
            } catch (RuntimeException $exception) {
                return false;
            }
        }
        return false;
    }

    /**
     * Create a settlement request. request.
     *
     * @deprecated Since ver 1.0 you should use `settlement()` instead this method. available until version 3.
     *
     * @return bool
     */
    public function regPayRequest()
    {
        return $this->settlement();
    }
}
