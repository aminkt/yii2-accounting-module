<?php

namespace aminkt\userAccounting\interfaces;


/**
 * Interface AccountingInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface AccountingInterface
{
    /**
     * Return a accounting model.
     *
     * @param string $meta
     * @param integer|\yii\web\IdentityInterface $user
     * @return mixed
     */
    public static function get($meta, $user);

    /**
     * Return key value.
     *
     * @param string $key
     * @param \yii\web\IdentityInterface $userIdentity
     *
     * @return string
     */
    public static function getValue($key, $userIdentity = null);

    /**
     * Return user amount.
     *
     * @param \yii\web\IdentityInterface $userIdentity
     *
     * @return float
     */
    public static function getAmount($userIdentity = null);

    /**
     * Create a deposit transaction from selected purse.
     *
     * @param float $amount Amount
     * @param PurseInterface $purse Purse object
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return TransactionInterface
     */
    public static function deposit($amount, $purse, $userIdentity = null);

    /**
     * Create a whitdraw transaction from selected purse.
     *
     * @param float $amount amount
     * @param PurseInterface $purse Purse object
     * @param \yii\web\IdentityInterface $userIdentity $userIdentity Owner identity object.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return TransactionInterface
     */
    public static function withdraw($amount, $purse, $userIdentity = null);

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
    public static function settlementRequest($amount, $purse, $account, $description = null, $type = SettlementRequestInterface::TYPE_SHABA, $userIdentity = null);

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
    public static function blockSettlementRequest($request, $note = null, $userIdentity = null);

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
    public static function confirmSettlementRequest($request, $bankTrackingCode, $note = null, $userIdentity = null);

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
    public static function rejectSettlementRequest($request, $note = null, $userIdentity = null);

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
    public static function createPurse($userIdentity, $name, $description = null);

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
    public static function blockPurse($purse, $note = null, $userIdentity = null);

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
    public static function unblockPurse($purse, $note = null, $userIdentity = null);

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
    public static function removePurse($purse, $force = false, $userIdentity = null);

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
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null);

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
    public static function confirmAccount($account, $note = null, $userIdentity = null);

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
    public static function blockAccount($account, $note = null, $userIdentity = null);

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
    public static function removeAccount($account, $force = false, $userIdentity = null);
}