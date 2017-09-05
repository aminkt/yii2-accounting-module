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
}