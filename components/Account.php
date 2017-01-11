<?php
namespace userAccounting\components;


use userAccounting\models\Transaction;
use userAccounting\models\UserAccounting;
use yii\base\Component;
use yii\web\NotFoundHttpException;

class Account extends Component
{
    /**
     * Return user account amount.
     * @param integer $type
     * @param null|integer $userId
     * @return float
     */
    public static function getAccountAmount($type=UserAccounting::TYPE_BALANCE, $userId=null){
        $userId = $userId?$userId:\Yii::$app->getUser()->getId();

        $account = UserAccounting::findOne([
            'userId'=>$userId,
            'type'=>$type
        ]);

        if($account)
            return $account->amount;

        return 0;
    }

    public static function initializeNewAccount($userId){
        $account = UserAccounting::find()->where(['userId'=>$userId])->all();
        if($account){
            return false;
        }

        $account = new UserAccounting();
        $account->userId = $userId;
        $account->amount = 0;
        $account->type = UserAccounting::TYPE_BALANCE;
        $account->save(false);

        $account = new UserAccounting();
        $account->userId = $userId;
        $account->amount = 0;
        $account->type = UserAccounting::TYPE_COSTS;
        $account->save(false);

        $account = new UserAccounting();
        $account->userId = $userId;
        $account->amount = 0;
        $account->type = UserAccounting::TYPE_INCOME;
        $account->save(false);
    }

    /**
     * Deposit amount to user account
     * @param $amount
     * @param int $type
     * @param null|string $description
     * @param null|integer $userId
     * @return bool|float
     * @throws NotFoundHttpException
     */
    public static function deposit($amount, $type=Transaction::TYPE_UNKNOWN, $description=null, $userId=null){
        $userId = $userId?$userId:\Yii::$app->getUser()->getId();

        $account = UserAccounting::findOne([
            'userId'=>$userId,
            'type'=>UserAccounting::TYPE_BALANCE
        ]);
        if(!$account)
            throw new NotFoundHttpException("متاسفانه حساب مورد نظر پیدا نشد.");


        $transaction = new Transaction();
        $transaction->userId = $account->userId;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->description = $description;
        $transaction->time = time();
        if($transaction->save()){
            $deposit = $account->deposit($amount);

            if($deposit===false)
                $transaction->delete();

            return $deposit;
        }else{
            \Yii::error($transaction->getErrors(), $transaction::className());
        }

        return false;

    }

    /**
     * Withdrawal amount from user account
     * @param $amount
     * @param int $type
     * @param null|string $description
     * @param null|integer $userId
     * @return bool|float
     * @throws NotFoundHttpException
     */
    public static function withdrawal($amount, $type=Transaction::TYPE_UNKNOWN, $description=null, $userId=null){
        $userId = $userId?$userId:\Yii::$app->getUser()->getId();

        $account = UserAccounting::findOne([
            'userId'=>$userId,
            'type'=>UserAccounting::TYPE_BALANCE
        ]);
        if(!$account)
            throw new NotFoundHttpException("متاسفانه حساب مورد نظر پیدا نشد.");


        $transaction = new Transaction();
        $transaction->userId = $account->userId;
        $transaction->amount = (-$amount);
        $transaction->type = $type;
        $transaction->description = $description;
        $transaction->time = time();
        if($transaction->save()){
            $withdrawal = $account->withdrawal($amount);
            if($withdrawal===false)
                $transaction->delete();

            return $withdrawal;
        }else{
            \Yii::error($transaction->getErrors(), $transaction::className());
        }

        return false;
    }
}