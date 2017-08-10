<?php

use yii\db\Migration;

class m170110_111551_init extends Migration
{

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable("{{%user_accounting_accounts}}",[
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer(),
            'bankName'=>$this->string(),
            'cardNumber'=>$this->string(),
            'accountNumber'=>$this->string(),
            'shaba'=>$this->string(),
            'owner'=>$this->string(),
            'amountPaid'=>$this->double()->defaultValue(0),
            'status'=>$this->smallInteger(2),
            'updateTime'=>$this->integer(20),
            'createTime'=>$this->integer(20),
        ]);

        $this->createTable("{{%user_accounting_pay_requests}}",[
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer(),
            'accountId'=>$this->integer(),
            'amount'=>$this->double()->defaultValue(0),
            'bankTrackingCode'=>$this->string(),
            'status'=>$this->smallInteger(2),
            'payTime'=>$this->integer(20)->null()->defaultValue(null),
            'updateTime'=>$this->integer(20),
            'createTime'=>$this->integer(20)
        ]);

        // add foreign key for table `catalog` and `catalog`
        $this->addForeignKey(
            'fk-user_accounting_pay_requests-accountId',
            '{{%user_accounting_pay_requests}}',
            'accountId',
            '{{%user_accounting_accounts}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable("{{%user_accounting_transactions}}",[
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer(),
            'amount'=>$this->double()->defaultValue(0),
            'remains'=>$this->double()->defaultValue(0),
            'description'=>$this->text(),
            'type'=>$this->smallInteger(2),
            'time'=>$this->integer(20),
        ]);

        $this->createTable("{{%user_accounting}}",[
            'userId'=>$this->integer(),
            'type'=>$this->smallInteger(2),
            'amount'=>$this->double()->defaultValue(0),
        ]);
        $this->addPrimaryKey(
            'pk-user_accounting',
            '{{%user_accounting}}',
            [
                'userId',
                'type',
            ]
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_accounting_pay_requests-accountId', '{{%user_accounting_pay_requests}}');

        $this->dropPrimaryKey('fk-user_accounting-accountId', '{{%user_accounting}}');

        $this->dropTable('{{%user_accounting}}');
        $this->dropTable('{{%user_accounting_transactions}}');
        $this->dropTable('{{%user_accounting_pay_requests}}');
        $this->dropTable('{{%user_accounting_accounts}}');
    }

}
