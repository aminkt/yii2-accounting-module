<?php
/**
 * Created by PhpStorm.
 * User: Amin
 * Date: 8/29/2017
 * Time: 08:13 PM
 */

namespace aminkt\userAccounting\interfaces;


interface UserAccountingInterface
{
    /**
     * @param integer|UserInterface $fromUser
     * @param integer|UserInterface $toUser
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return bool
     */
    public static function migrate($fromUser, $toUser);

    /**
     * Delete object (Purse, Account or Settlement request)
     *
     * @return boolean
     */
    public function delete();
}