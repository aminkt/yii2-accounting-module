<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\interfaces\AccountingInterface;
use aminkt\userAccounting\interfaces\SettlementRequestInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%useraccounting}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $meta
 * @property string $value
 * @property integer $time
 */
class UserAccounting extends ActiveRecord implements AccountingInterface
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['time', 'time'],
                ],
            ],
        ];
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
            [['userId', 'meta'], 'required'],
            [['userId'], 'integer'],
            [['meta', 'value'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => 'User ID',
            'meta' => 'Meta',
            'value' => 'Value',
            'time' => 'Time',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function get($meta, $user)
    {
        if ($user instanceof IdentityInterface)
            $user = $user->getId();

        $data = self::findOne([
            'userId' => $user,
            'meta' => $meta
        ]);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function getValue($key, $userIdentity = null)
    {
        $userIdentity = static::getUser($userIdentity);
        $model = self::get($key, $userIdentity);
        if ($model)
            return $model->value;
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function getAmount($userIdentity = null)
    {
        if (is_integer($userIdentity))
            $userId = $userIdentity;
        else
            $userId = static::getUser($userIdentity)->getId();
        $amount = 0;
        /** @var \aminkt\userAccounting\models\Purse[] $purses */
        $purses = Purse::find()->where(['userId' => $userId])->all();
        foreach ($purses as $purse) {
            $amount += $purse->getAmount();
        }
        return $amount;
    }

    /**
     * @inheritdoc
     */
    public static function deposit($amount, $purse, $description)
    {
        if (is_integer($purse)) {
            $purse = Purse::findOne($purse);
        }
        if ($purse) {
            return $purse->deposit($amount, $description);
        }

        throw new InvalidArgumentException("Purse is not a valid purse.");
    }

    /**
     * @inheritdoc
     */
    public static function withdraw($amount, $purse, $description)
    {
        return $purse->withdraw($amount, $description);
    }

    /**
     * @inheritdoc
     */
    public static function settlementRequest($amount, $purse, $account, $description = null, $type = SettlementRequestInterface::TYPE_SHABA)
    {
        $settlement = Settlement::createSettlementRequest($amount, $purse, $account, $description, $type);
        return $settlement;
    }

    /**
     * @inheritdoc
     */
    public static function blockSettlementRequest($request, $note = null)
    {
        return $request->blockSettlementRequest($note);
    }

    /**
     * @inheritdoc
     */
    public static function confirmSettlementRequest($request, $bankTrackingCode, $note = null)
    {
        return $request->confirmSettlementRequest($bankTrackingCode, $note);
    }

    /**
     * @inheritdoc
     */
    public static function rejectSettlementRequest($request, $note = null)
    {
        return $request->rejectSettlementRequest($note);
    }

    /**
     * @inheritdoc
     */
    public static function createPurse($userIdentity, $name, $description = null)
    {
        $purse = Purse::createPurse($userIdentity, $name, $description);
        return $purse;
    }

    /**
     * @inheritdoc
     */
    public static function blockPurse($purse, $note = null)
    {
        return $purse->blockPurse($note);
    }

    /**
     * @inheritdoc
     */
    public static function unblockPurse($purse, $note = null)
    {
        return $purse->unblockPurse($note);
    }

    /**
     * @inheritdoc
     */
    public static function removePurse($purse, $force = false)
    {
        return $purse->removePurse($force);
    }

    /**
     * @inheritdoc
     */
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null)
    {
        $account = Account::createAccount($userIdentity, $bankName, $owner, $cardNumber, $shaba, $accountNumber);
        return $account;
    }

    /**
     * @inheritdoc
     */
    public static function confirmAccount($account, $note = null)
    {
        return $account->confirmAccount($note);
    }

    /**
     * @inheritdoc
     */
    public static function blockAccount($account, $note = null)
    {
        return $account->blockAccount($note);
    }

    /**
     * @inheritdoc
     */
    public static function removeAccount($account, $force = false)
    {
        return $account->removeAccount();
    }

    /**
     * @inheritdoc
     */
    private static function getUser($userIdentity = null)
    {
        if (!$userIdentity)
            return \Yii::$app->getUser()->getIdentity();

        return $userIdentity;
    }

    /**
     * @inheritdoc
     */
    public static function migrate($fromUser, $toUser)
    {
        $from = is_integer($fromUser) ? $fromUser : static::getUser($fromUser)->getId();

        $to = is_integer($toUser) ? $toUser : static::getUser($toUser)->getId();

        /** @var \aminkt\userAccounting\interfaces\UserAccountingInterface[] $models */
        $models = [
            UserAccounting::className(),
            Purse::className(),
            Settlement::className(),
            Account::className(),
            \aminkt\userAccounting\UserAccounting::getInstance()->transactionModel,
        ];
        foreach ($models as $model) {
            $model::migrate($from, $to);
        }
        return true;
    }
}
