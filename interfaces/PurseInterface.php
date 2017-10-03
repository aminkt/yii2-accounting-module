<?php

namespace aminkt\userAccounting\interfaces;

/**
 * Interface PurseInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface PurseInterface extends UserAccountingInterface
{
    /** Settlement request status */
    const STATUS_WAITING = 1;
    /** Settlement request status */
    const STATUS_CONFIRMED = 2;
    /** Settlement request status */
    const STATUS_BLOCKED = 3;
    /** Settlement request status */
    const STATUS_REMOVED = 4;

    const AUTO_SETTLEMENT_ON = 1;
    const AUTO_SETTLEMENT_OFF = 0;

    /**
     * Return purse id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return purse user id
     *
     * @return integer
     */
    public function getUserId();

    /**
     * Create a new purse.
     *
     * @param \yii\web\IdentityInterface $userIdentity Owner identity object.
     * @param string $name Name of purse
     * @param string|null $description
     *
     * @throws \aminkt\userAccounting\exceptions\\RuntimeException Throw if process stop unexpectly.
     *
     * @return PurseInterface
     */
    public static function createPurse($userIdentity, $name, $description = null);

    /**
     * Edit purse information.
     *
     * @param string $name
     * @param string $description
     *
     * @throws \aminkt\userAccounting\exceptions\\RuntimeException Throw if process stop unexpectly.
     *
     * @return PurseInterface
     */
    public function edit($name = null, $description = null);

    /**
     * Return amount of purse.
     *
     * @return double
     */
    public function getAmount();

    /**
     * Create a deposit transaction from selected purse.
     *
     * @param float $amount Amount
     * @param string $description Transaction description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException
     *
     * @return TransactionInterface
     */
    public function deposit($amount, $description = null);

    /**
     * Create a whitdraw transaction from selected purse.
     *
     * @param float $amount amount
     * @param string $description Transaction description
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return TransactionInterface
     */
    public function withdraw($amount, $description = null);

    /**
     * Block a purse.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function blockPurse($note = null);

    /**
     * Unblock a blocked purse.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function unblockPurse($note = null);

    /**
     * Remove a purse.
     *
     * @param bool $force If purse is not emmoty then process will stop. by setting this value to true, purse will delte even if have amount.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public function removePurse($force = false);

    /**
     * Assign an account to purse.
     *
     * @param integer|\aminkt\userAccounting\interfaces\AccountInterface $account Account that should assign to purse.
     * @param bool $autoSettlement If true auto settlement turned on and if false turned off.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return \aminkt\userAccounting\interfaces\PurseInterface
     */
    public function setAccount($account, $autoSettlement = true);

    /**
     * Remove assignment of an account from purse.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return \aminkt\userAccounting\interfaces\PurseInterface
     */
    public function removeAccount();

    /**
     * Set on auto settlement of purse if account was assigned.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return boolean
     */
    public function autoSettlementOn();

    /**
     * Turn off auto settlement of purse.
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return void
     */
    public function autoSettlementOff();
}