<?php
namespace aminkt\userAccounting\components;

use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\models\Purse;
use aminkt\userAccounting\models\Settlement;
use aminkt\userAccounting\models\Transaction;
use aminkt\userAccounting\models\UserAccounting;
use yii\base\Component;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * Class Account
 * @author Amin Keshavarz <ak_1596@yahoo.com> 20/8/2017
 *
 * @package aminkt\userAccounting\components
 */
class Account extends Component
{
    /**
     * Return user account amount.
     *
     * @param null|integer|\yii\web\IdentityInterface $user
     *
     * @return float
     */
    public static function getBalance($user)
    {
        try {
            $amount = UserAccounting::getAmount($user);
        } catch (RuntimeException $e) {
            \Yii::warning($e->getTrace(), $e->getMessage());
            $amount = 0;
        }
        return $amount;
    }

    /**
     * Deposit amount to user purse. if purse not defined will deposit to default purse.
     * <code>
     * $args = [
     *  'amount'=>2000,
     *  'description'=>'Transaction description',
     *  'purse'=>$purse,
     *  'user'=>$user,
     * ]
     * Account::deposit($arg);
     * </code>
     *
     * @param array $args Method arguments. user like this:
     *
     * @internal double                                                         $amount         Transaction amount. Required.
     * @internal string                                                         $description    Transaction description. Optional.
     * @internal null|integer|\aminkt\userAccounting\interfaces\PurseInterface  $purse          Purse id or object. Optional.
     * @internal null|integer|\yii\web\IdentityInterface                        $user           User id or object. Optional.
     * @return bool
     *
     */
    public static function deposit($args = [])
    {
        $amount = ArrayHelper::getValue($args, 'amount', 0);
        $description = ArrayHelper::getValue($args, 'description');
        $purse = ArrayHelper::getValue($args, 'purse');
        $user = ArrayHelper::getValue($args, 'user');

        if (!$purse and $user) {
            if ($user instanceof IdentityInterface)
                $user = $user->getId();
            $purse = Purse::find()->where(['userId' => $user])->orderBy(['id' => SORT_ASC])->one();
        }
        $transaction = UserAccounting::deposit($amount, $purse, $description);
        if ($transaction)
            return true;
        return false;
    }

    /**
     * Withdraw amount from user account.
     * <code>
     * $args = [
     *  'amount'=>2000,
     *  'description'=>'Transaction description',
     *  'purse'=>$purse,
     *  'user'=>$user,
     * ]
     * Account::withdrawal($arg);
     * </code>
     *
     * @param array $args Method arguments. user like this:
     *
     * @internal double                                                         $amount         Transaction amount. Required.
     * @internal string                                                         $description    Transaction description. Optional.
     * @internal null|integer|\aminkt\userAccounting\interfaces\PurseInterface  $purse          Purse id or object. Optional.
     * @internal null|integer|\yii\web\IdentityInterface                        $user           User id or object. Optional.
     * @return bool
     *
     */
    public static function withdrawal($args = [])
    {
        $amount = ArrayHelper::getValue($args, 'amount', 0);
        $description = ArrayHelper::getValue($args, 'description');
        $purse = ArrayHelper::getValue($args, 'purse');
        $user = ArrayHelper::getValue($args, 'user');

        if (!$purse and $user) {
            if ($user instanceof IdentityInterface)
                $user = $user->getId();
            $purse = Purse::find()->where(['userId' => $user])->orderBy(['id' => SORT_ASC])->one();
        }
        $transaction = UserAccounting::withdraw($amount, $purse, $description);
        if ($transaction)
            return true;
        return false;
    }

    /**
     * Return user account amount.
     *
     * @param integer $type
     * @param null|integer $userId
     *
     * @deprecated Use <b>getBalance($user)</b>. this method not developing any more.
     *
     * @see Account::getBalance()
     *
     * @return float
     */
    public static function getAccountAmount($type = null, $userId = null)
    {
        return self::getBalance($userId);
    }

    /**
     * Initialize accounting system for a user.
     *
     * @param null|integer|\yii\web\IdentityInterface $user
     * @param string $purseName
     * @param string $purseDescription
     *
     * @return bool
     */
    public static function initializeNewAccount($user, $purseName = 'Default', $purseDescription = 'Default purse of user.')
    {
        $purse = UserAccounting::createPurse($user, $purseName, $purseDescription);
        if ($purse)
            return true;
        return false;
    }

    /**
     * Migrate all account data to another user.
     *
     * @param integer $fromUserId The user id that we want migrate from that.
     * @param integer $toUserId The user id that we want migrate ti that.
     *
     * @return boolean
     */
    public static function migrateAccount($fromUserId, $toUserId){
        return UserAccounting::migrate($fromUserId, $toUserId);
    }

    /**
     * Magic method to process any dynamic method calls.
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        } else {
            return call_user_func_array([UserAccounting::className(), $method], $arguments);
        }
    }
}