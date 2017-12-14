<?php

use yii\db\Migration;

/**
 * Class m171129_065410_add_transaction_tbl
 */
class m171129_065410_add_transaction_tbl extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%user_accounting_transactions}}", [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'purseId' => $this->integer(),
            'amount' => $this->double()->defaultValue(0),
            'purseRemains' => $this->double()->defaultValue(0),
            'totalRemains' => $this->double()->defaultValue(0),
            'description' => $this->text(),
            'type' => $this->smallInteger(2),
            'time' => $this->dateTime() . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ], $tableOptions);

//        $this->addForeignKey('transaction_user_userId_fk', '{{%user_accounting_transactions}}', 'userId', '{{%user}}', 'id', 'SET NULL', 'SET NULL');
        $this->addForeignKey('transaction_purse_purseId_fk', '{{%user_accounting_transactions}}', 'purseId', '{{%user_accounting_purses}}', 'id', 'SET NULL', 'SET NULL');

    }

    public function safeDown()
    {
        if (\aminkt\userAccounting\UserAccounting::getInstance()->transactionModel == 'aminkt\userAccounting\models\Transaction') {
            $this->dropForeignKey('transaction_purse_purseId_fk', '{{%user_accounting_transactions}}');
//        $this->dropForeignKey('transaction_user_userId_fk', '{{%user_accounting_transactions}}');
            $this->dropTable('{{%user_accounting_transactions}}');
        } else {
            echo "Transaction table migration down canceled because module configuration use custom Transaction table.";
        }
    }
}
