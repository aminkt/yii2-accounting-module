<?php

namespace aminkt\userAccounting\models;

use aminkt\normalizer\Validation;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\AccountInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_accounting_accounts}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $bankName
 * @property string $cardNumber
 * @property string $accountNumber
 * @property string $shaba
 * @property string $owner
 * @property integer $status
 * @property string $operatorNote
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property Settlement[] $settlements
 * @property \aminkt\userAccounting\interfaces\UserInterface $user
 */
class Account extends ActiveRecord implements AccountInterface
{
    const SCENARIO_UPDATE = 'update';

    protected $user;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_accounts}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createTime', 'updateTime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updateTime'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                // 'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'status', 'bankName', 'shaba'], 'required'],
            [['userId', 'status', 'updateTime', 'createTime'], 'integer'],
            [['operatorNote'], 'string'],
            [['cardNumber'], 'validateCardNumber'],
            [['shaba'], 'validateIBAN'],
            [['bankName', 'cardNumber', 'accountNumber', 'shaba', 'owner'], 'string', 'max' => 255],
        ];
    }

    /**
     * Validate IBAN.
     *
     * @param $attribute
     * @param $params
     * @param $validator
     *
     */
    public function validateIBAN($attribute, $params, $validator)
    {
        if ($iban = Validation::validateIBAN($this->$attribute)) {
            $this->shaba = $iban;
        } else {
            $this->addError($attribute, 'شماره شبا وارد شده معتبر نیست.');
        }
    }

    /**
     * Validate Credit card number.
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateCardNumber($attribute, $params, $validator)
    {
        if ($card = Validation::validateCreditCard($this->$attribute)) {
            $this->cardNumber = $card;
        } else {
            $this->addError($attribute, 'شماره کارت وارد شده معتبر نیست.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'bankName' => 'نام بانک',
            'cardNumber' => 'شماره کارت',
            'accountNumber' => 'شماره حساب',
            'shaba' => 'شبا',
            'owner' => 'صاحب حساب',
            'amountPaid' => 'مبلغ واریز شده (تومان)',
            'status' => 'وضعیت',
            'operatorNote'=>'یادداشت اپراتور',
            'updateTime' => 'Update Time',
            'createTime' => 'Create Time',
        ];
    }

    /**
     * Return user model.
     *
     * @return \aminkt\userAccounting\interfaces\UserInterface
     */
    public function getUser()
    {
        if (!$this->user) {
            /** @var \aminkt\userAccounting\interfaces\UserInterface $userModel */
            $userModel = \aminkt\userAccounting\UserAccounting::getInstance()->userModel;
            $this->user = $userModel::findOne($this->userId);
        }

        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['bankName', 'cardNumber', 'accountNumber','shaba','owner','status','operatorNote'];
        return $scenarios;
    }

    /**
     * Delete Account object by changing status to removed.
     *
     * @return bool
     */
    public function delete()
    {
        if ($this->beforeDelete()) {
            $this->status = self::STATUS_REMOVED;
            if ($this->save(false)) {
                $this->afterDelete();
                return true;
            }
            \Yii::error($this->getErrors(), self::className());
            throw new \RuntimeException("Can not delete purse.");
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettlements()
    {
        return $this->hasMany(Settlement::className(), ['accountId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null)
    {
        $account = new static();
        $account->userId = $userIdentity->getId();
        $account->bankName = $bankName;
        $account->owner = $owner;
        $account->cardNumber = $cardNumber;
        $account->shaba = $shaba;
        $account->accountNumber = $accountNumber;
        $account->status = self::STATUS_WAITING;
        if ($account->save())
            return $account;

        \Yii::error($account->getErrors(), self::class);
        throw new RuntimeException("Account model creation become failed");
    }

    /**
     * @inheritdoc
     */
    public function edit($bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null)
    {

        if ($bankName)
            $this->bankName = $bankName;

        if ($owner)
            $this->owner = $owner;

        if ($cardNumber)
            $this->cardNumber = $cardNumber;

        if ($shaba)
            $this->shaba = $shaba;

        if ($accountNumber)
            $this->accountNumber = $accountNumber;


        $this->status = self::STATUS_WAITING;
        if ($this->save())
            return $this;

        \Yii::error($this->getErrors(), self::class);
        throw new RuntimeException("Account model edit become failed");
    }

    /**
     * @inheritdoc
     */
    public function confirmAccount($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_CONFIRMED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account confirmation become failed");
        }
    }

    /**
     * @inheritdoc
     */
    public function blockAccount($note = null)
    {
        $this->operatorNote = $note;
        $this->status = self::STATUS_BLOCKED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account blocking become failed");
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAccount()
    {
        $this->status = self::STATUS_REMOVED;
        if (!$this->save()) {
            \Yii::error($this->getErrors(), self::class);
            throw new RuntimeException("Account removing become failed");
        }
    }

    /**
     * @param integer $fromUser
     * @param integer $toUser
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return bool
     */
    public static function migrate($fromUser, $toUser)
    {
        $q = new \yii\db\Query();
        $q->createCommand()->update(self::tableName(), ['userId' => $toUser], ['userId' => $fromUser])->execute();
        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Find account by userId.
     * @param integer $userId
     * @return static[]
     */
    public static function findByUserId($userId)
    {
        return static::findAll(['userId' => $userId, 'status' => self::STATUS_CONFIRMED]);
    }
}
