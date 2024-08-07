<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\widgets\alert\Alert;
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
            [['amount'], 'number', 'min'=>\aminkt\userAccounting\UserAccounting::getInstance()->minSettlementAmount, 'max'=>\aminkt\userAccounting\UserAccounting::getInstance()->maxSettlementAmount],
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
                $purse = Purse::findOne($this->purse);
                if(!$purse)
                    throw new InvalidArgumentException("Purse not found.");
                $account = Account::findOne($this->account);
                $settlement = UserAccounting::settlementRequest(floatval($this->amount), $purse, $account, $this->description, $this->type);
                return true;
            } catch (RuntimeException $exception) {
                return false;
            } catch (InvalidArgumentException $exception) {
                Alert::error("خطا در ایجاد درخواست تسویه", "کیف پول انتخابی، موجودی کافی ندارد.");
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
