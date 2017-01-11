<?php

use yii\db\Migration;

class m170110_111551_init extends Migration
{

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable("{{%userAccounting_accounts}}",[
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

        $this->createTable("{{%userAccounting_pay_requests}}",[
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
            'fk-userAccounting_pay_requests-accountId',
            '{{%userAccounting_pay_requests}}',
            'accountId',
            '{{%userAccounting_accounts}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable("{{%userAccounting_transactions}}",[
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer(),
            'amount'=>$this->double()->defaultValue(0),
            'description'=>$this->text(),
            'type'=>$this->smallInteger(2),
            'time'=>$this->integer(20),
        ]);

        $this->createTable("{{%userAccounting}}",[
            'userId'=>$this->integer(),
            'type'=>$this->smallInteger(2),
            'amount'=>$this->double()->defaultValue(0),
        ]);
        $this->addPrimaryKey(
            'pk-userAccounting',
            '{{%userAccounting}}',
            [
                'userId',
                'type',
            ]
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-userAccounting_pay_requests-accountId', '{{%userAccounting_pay_requests}}');

        $this->dropPrimaryKey('fk-userAccounting-accountId', '{{%userAccounting}}');

        $this->dropTable('{{%userAccounting}}');
        $this->dropTable('{{%userAccounting_transactions}}');
        $this->dropTable('{{%userAccounting_pay_requests}}');
        $this->dropTable('{{%userAccounting_accounts}}');
    }

}
