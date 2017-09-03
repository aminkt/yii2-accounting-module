<?php

use yii\db\Migration;
use yii\helpers\Console;

class m170818_140200_change_structure extends Migration
{
    public function safeUp()
    {
        if (false and !$this->confirm("Are you sure? All data will be remove and migrate down may not available.")) {
            $this->stdout('Operation cancelled' . PHP_EOL, Console::FG_RED);
            return false;
        }

        // Changes of accounts tabel
        $this->addColumn('{{%user_accounting_accounts}}', 'operatorNote', $this->text() . ' AFTER `status`');

        // Settlements table changes
        $this->renameTable("{{%user_accounting_pay_requests}}", "{{%user_accounting_settlements}}");
        $this->addColumn('{{%user_accounting_settlements}}', 'purseId', $this->integer() . ' AFTER `userId`');
        $this->addColumn('{{%user_accounting_settlements}}', 'description', $this->text() . ' AFTER `amount`');
        $this->addColumn('{{%user_accounting_settlements}}', 'operatorNote', $this->text() . ' AFTER `description`');
        $this->addColumn('{{%user_accounting_settlements}}', 'settlementType', $this->smallInteger(2) . ' AFTER `operatorNote`');
        $this->renameColumn("{{%user_accounting_settlements}}", "payTime", "settlementTime");

        // Accounting table cahnges
        $this->dropTable('{{%user_accounting}}');

        $this->createTable("{{%user_accounting}}", [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'meta' => $this->string(64)->notNull(),
            'value' => $this->string(),
            'time' => $this->integer(20),
        ]);


        // Create purse table
        $this->createTable("{{%user_accounting_purses}}", [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'accountId' => $this->integer(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'operatorNote' => $this->text(),
            'autoSettlement' => $this->smallInteger(1)->defaultValue(0),
            'status' => $this->smallInteger(2),
            'updateTime' => $this->integer(20),
            'createTime' => $this->integer(20),
        ]);

        $this->addForeignKey(
            'fk-user_accounting_purses-accountId',
            '{{%user_accounting_purses}}',
            'accountId',
            '{{%user_accounting_accounts}}',
            'id',
            'SET NULL',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk-user_accounting_settelemets-purseId',
            '{{%user_accounting_settlements}}',
            'purseId',
            '{{%user_accounting_purses}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    private function confirm($message)
    {
        echo "$message  Type 'yes' to continue: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) != 'yes') {
            echo "ABORTING!\n";
            return false;
        }
        fclose($handle);
        echo "\n";
        echo "Thank you, continuing...\n";
        return true;
    }

    public function safeDown()
    {
        echo "Migrate down is not available in this version.";

        return false;
    }
}
