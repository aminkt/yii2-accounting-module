<?php

namespace aminkt\userAccounting\interfaces;


/**
 * Interface AccountingInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface AccountingInterface extends UserAccountingInterface
{
    const META_DEFAULT_PURSE = 'default_purse';

    /**
     * Return a accounting model.
     *
     * @param string $meta
     * @param integer|UserInterface $user
     * @return AccountingInterface
     */
    public static function get($meta, $user);

    /**
     * Return key value.
     *
     * @param string $key
     * @param integer|UserInterface $userIdentity
     *
     * @return string
     */
    public static function getValue($key, $userIdentity = null);

    /**
     * Calculate and return user amount.
     *
     * @param integer|UserInterface $userIdentity
     *
     * @return float
     */
    public static function getAmount($userIdentity = null);

    /**
     * Create a deposit transaction from selected purse.
     *
     * @param float $amount Amount
     * @param integer|PurseInterface $purse Purse object
     * @param string $description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException Throw if purse is not valid.
     *
     * @return TransactionInterface
     */
    public static function deposit($amount, $purse, $description);

    /**
     * Create a whitdraw transaction from selected purse.
     *
     * @param float $amount amount
     * @param PurseInterface $purse Purse object
     * @param string $description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return TransactionInterface
     */
    public static function withdraw($amount, $purse, $description);

    /**
     * Create a settlement request from selected purse to selected account.
     *
     * @param float $amount
     * @param PurseInterface $purse
     * @param AccountInterface $account
     * @param string|null $description
     * @param int $type
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return SettlementRequestInterface
     */
    public static function settlementRequest($amount, $purse, $account, $description = null, $type = SettlementRequestInterface::TYPE_SHABA);

    /**
     * Block a settlement request.
     *
     * @param SettlementRequestInterface $request
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function blockSettlementRequest($request, $note = null);

    /**
     * Confirm a settlment request.
     *
     * @param SettlementRequestInterface $request
     * @param string $bankTrackingCode Bank trakcing code for loging.
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function confirmSettlementRequest($request, $bankTrackingCode, $note = null);

    /**
     * Reject a settlement request.
     *
     * @param SettlementRequestInterface $request
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function rejectSettlementRequest($request, $note = null);

    /**
     * Create a new purse.
     *
     * @param integer|UserInterface $user Owner identity object or id.
     * @param string $name Name of purse
     * @param string|null $description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return PurseInterface
     */
    public static function createPurse($user, $name, $description = null);

    /**
     * Block a purse.
     *
     * @param PurseInterface $purse
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function blockPurse($purse, $note = null);

    /**
     * Unblock a blocked purse.
     *
     * @param PurseInterface $purse
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function unblockPurse($purse, $note = null);

    /**
     * Remove a purse.
     *
     * @param PurseInterface $purse
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function removePurse($purse, $force = false);

    /**
     * Create a bank account.
     *
     * @param UserInterface $userIdentity $userIdentity Owner identity object.
     * @param string|null $bankName Account bank name.
     * @param string|null $owner Account owner name
     * @param string|null $cardNumber Account card number
     * @param string|null $shaba Account Shaba number
     * @param string|null $accountNumber Account nummber
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return AccountInterface
     */
    public static function createAccount($userIdentity, $bankName = null, $owner = null, $cardNumber = null, $shaba = null, $accountNumber = null);

    /**
     * Confirm an account.
     *
     * @param AccountInterface $account
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function confirmAccount($account, $note = null);

    /**
     * Block an account.
     *
     * @param AccountInterface $account
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public static function blockAccount($account, $note = null);

    /**
     * Remove an account.
     *
     * @param AccountInterface $account
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     *
     * @return void
     */
    public static function removeAccount($account, $force = false);

    /**
     * Return default purse object of selected user.
     *
     * @param integer|UserInterface $user
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException When user not defined or not correct.
     *
     * @return void
     */
    public static function getDefaultPurse($user);

    /**
     * Set default purse as defined purse for selected user.
     *
     * @param integer|UserInterface $user
     * @param integer|PurseInterface $purse
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException When user or purse not defined or not correct.
     *
     * @return void
     */
    public static function setDefaultPurse($user, $purse);
}