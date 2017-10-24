<?php

namespace aminkt\userAccounting\interfaces;

use yii\db\ActiveRecordInterface;
use yii\web\IdentityInterface;

/**
 * Interface TransactionInterface
 *
 * @property integer $id
 *
 * @author Amin Keshavarz <Ak_1596@yahoo.com)
 * @package aminkt\userAccounting\interfaces
 */
interface UserInterface extends IdentityInterface, ActiveRecordInterface
{
    const USER_STATUS_REMOVED = 0;
    const USER_STATUS_ACTIVE = 1;
    const USER_STATUS_WAITING = 2;
    const USER_STATUS_BLOCKED = 3;

    /**
     * Return name of the user.
     *
     * @return string
     */
    public function getName();

    /**
     * Return family of the user.
     *
     * @return mixed
     */
    public function getFamily();

    /**
     * Return full name of user.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Return mobile number of user.
     *
     * @return string
     */
    public function getMobile();

    /**
     * Return email of user.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Return status of user.
     *
     * @return mixed
     */
    public function getStatus();
}