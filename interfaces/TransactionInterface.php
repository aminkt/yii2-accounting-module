<?php

namespace aminkt\userAccounting\interfaces;

/**
 * Interface TransactionInterface
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface TransactionInterface
{
    /** Transaction type. Normal transaction. */
    const TYPE_NORMAL = 1;
    /** Transaction type. Deposit to purse becuse of gift. */
    const TYPE_GIFT = 2;
    /** Transaction type. Withdraw money from purse becuse of settlement reques.*/
    const TYPE_SETTLEMENT_REQUEST_WITHDRAW = 2;
    /** Transaction type. Return money to purse becuse of rejectment of request.*/
    const TYPE_SETTLEMENT_REQUEST_REJECTED = 3;
}