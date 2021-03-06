<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\AccountingInterface;
use aminkt\userAccounting\interfaces\PurseInterface;
use aminkt\userAccounting\interfaces\SettlementRequestInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
    public static function createPurse($user, $name, $description = null)
    {
        $purse = Purse::createPurse($user, $name, $description);
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

    /**
     * Return default purse object of selected user.
     *
     * @param integer|\aminkt\userAccounting\interfaces\UserInterface $user
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException When user not defined or not correct.
     *
     * @return PurseInterface
     */
    public static function getDefaultPurse($user)
    {
        $userId = is_integer($user) ? $user : static::getUser($user)->getId();
        $model = self::findOne(['userId' => $userId, 'meta' => self::META_DEFAULT_PURSE]);
        if (!$model)
            throw new InvalidArgumentException("Purse not found for defined user.");
        $purse = Purse::findOne($model->value);
        if (!$purse)
            throw new RuntimeException("Purse is not exist but information are saved before and not updated.");

        return $purse;
    }

    /**
     * Set default purse as defined purse for selected user.
     *
     * @param integer|\aminkt\userAccounting\interfaces\UserInterface $user
     * @param integer|PurseInterface $purse
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException When user or purse not defined or not correct.
     *
     * @return void
     */
    public static function setDefaultPurse($user, $purse)
    {
        $userId = is_integer($user) ? $user : static::getUser($user)->getId();
        $purseId = is_integer($purse) ? $purse : $purse->getId();

        $model = self::findOne(['userId' => $userId, 'meta' => self::META_DEFAULT_PURSE]);
        if (!$model) {
            $model = new self();
            $model->userId = $userId;
            $model->meta = self::META_DEFAULT_PURSE;
        }
        $model->value = $purseId;
        $model->save(false);
    }
}
