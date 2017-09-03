<?php

namespace aminkt\userAccounting\interfaces;

/**
 * Interface AccountInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface AccountInterface extends UserAccountingInterface
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
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function confirmAccount($note = null);

    /**
     * Block an account.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function blockAccount($note = null);

    /**
     * Remove an account.
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     *
     * @return void
     */
    public function removeAccount();
}