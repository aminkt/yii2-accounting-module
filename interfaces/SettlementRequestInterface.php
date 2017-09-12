<?php

namespace aminkt\userAccounting\interfaces;

/**
 * Interface SettlementRequestInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface SettlementRequestInterface extends UserAccountingInterface
{
    /** Settlement type */
    const TYPE_CART_TO_CART = 1;
    /** Settlement type */
    const TYPE_SHABA = 2;

    /** Settlement request status */
    const STATUS_WAITING = 1;
    /** Settlement request status */
    const STATUS_CONFIRMED = 2;
    /** Settlement request status */
    const STATUS_REJECTTED = 3;
    /** Settlement request status */
    const STATUS_BLOCKED = 4;
    /** Settlement request status */
    const STATUS_REMOVED = 5;

    /**
     * Create a settlement request from selected purse to selected account.
     *
     * @param float $amount
     * @param PurseInterface $purse Purse model
     * @param AccountInterface|integer $account Account model or database id.
     * @param string|null $description
     * @param int $type
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     * @throws \aminkt\userAccounting\exceptions\InvalidArgumentException Throw if amount is not valid.
     *
     * @return SettlementRequestInterface
     */
    public static function createSettlementRequest($amount, $purse, $account, $description = null, $type = self::TYPE_SHABA);

    /**
     * Block a settlement request.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function blockSettlementRequest($note = null);

    /**
     * Confirm a settlment request.
     *
     * @param string $bankTrackingCode Bank trakcing code for loging.
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function confirmSettlementRequest($bankTrackingCode, $note = null);

    /**
     * Reject a settlement request.
     *
     * @param string|null $note
     *
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectly.
     *
     * @return void
     */
    public function rejectSettlementRequest($note = null);
}