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
     * @param integer|\yii\web\IdentityInterface $fromUser
     * @param integer|\yii\web\IdentityInterface $toUser
     *
     * @throws \aminkt\userAccounting\exceptions\RiskException
     * @throws \aminkt\userAccounting\exceptions\RuntimeException Throw if process stop unexpectedly.
     *
     * @return bool
     */
    public static function migrate($fromUser, $toUser);
}