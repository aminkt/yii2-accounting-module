<?php

namespace aminkt\userAccounting\models;

use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\interfaces\TransactionInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_accounting_transactions}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $purseId
 * @property double $amount
 * @property double $purseRemains
 * @property double $totalRemains
 * @property string $description
 * @property integer $type
 * @property integer $time
 *
 * @property Purse $purse
 */
class Transaction extends \yii\db\ActiveRecord implements TransactionInterface
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['time', 'time'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_accounting_transactions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'type', 'amount'], 'required'],
            [['userId', 'type', 'time'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'amount' => 'مبلغ (تومان)',
            'description' => 'توضیحات',
            'type' => 'نوع تراکنش',
            'time' => 'زمان تراکنش',
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->userId = $this->purse->userId;
        }else{
            throw new RuntimeException("Transaction can not update.");
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurse()
    {
        return $this->hasOne(Purse::className(), ['id' => 'purseId']);
    }
}
