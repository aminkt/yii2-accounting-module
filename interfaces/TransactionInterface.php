<?php

namespace aminkt\userAccounting\interfaces;

/**
 * Interface TransactionInterface
 *
 * @property integer $userId
 * @property integer $purseId
 * @property double $amount
 * @property double $purseRemains
 * @property double $totalRemains
 * @property string $description
 * @property integer $type
 *
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface TransactionInterface extends UserAccountingInterface
{
    /** Transaction type. Normal transaction. */
    const TYPE_NORMAL = 1;
    /** Transaction type. Deposit to purse becuse of gift. */
    const TYPE_GIFT = 2;
    /** Transaction type. Withdraw money from purse becuse of settlement reques.*/
    const TYPE_SETTLEMENT_REQUEST_WITHDRAW = 2;
    /** Transaction type. Return money to purse becuse of rejectment of request.*/
    const TYPE_SETTLEMENT_REQUEST_REJECTED = 3;

    /**
     * Deposit amount to defined purse.
     *
     * @param double $amount
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     * @param string $description
     * @param integer $type
     *
     * @return boolean  True if deposit apply correctly and false if not.
     */
    public static function deposit($amount, $purse, $description, $type);

    /**
     * Deposit amount to defined purse.
     *
     * @param double $amount
     * @param \aminkt\userAccounting\interfaces\PurseInterface $purse
     * @param string $description
     * @param integer $type
     *
     * @return boolean  True if withdraw apply correctly and false if not.
     */
    public static function withdraw($amount, $purse, $description, $type);

    /**
     * Return purse object of transaction model.
     *
     * @return \aminkt\userAccounting\interfaces\PurseInterface
     */
    public function getPurse();

    /**
     * Return user object of transaction model.
     *
     * @return \yii\web\IdentityInterface
     */
    public function getUser();

    /**
     * Return amount of transaction.
     *
     * @return double
     */
    public function getAmount();

    /**
     * Return remaining money of purse after creating current transaction.
     *
     * @return double
     */
    public function getPurseRemain();

    /**
     * Return total remaining money of all user purses.
     *
     * @return double
     */
    public function getTotalRemain();

    /**
     * Return transaction description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Return type of transaction.
     *
     * @return integer
     */
    public function getType();

    /**
     * Return transaction time.
     *
     * @return integer|string
     */
    public function getTime();

    /**
     * Calculate total deposit of a purse.
     *
     * @param PurseInterface|integer $purse
     *
     * @return double   This number is positive
     */
    public static function getTotalPurseDeposit($purse);

    /**
     * Calculate total withdraw of a purse.
     *
     * @param PurseInterface|integer $purse
     *
     * @return double   This number is positive.
     */
    public static function getTotalPurseWithdraw($purse);
}