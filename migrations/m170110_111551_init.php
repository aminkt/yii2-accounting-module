<?php

use yii\db\Migration;

class m170110_111551_init extends Migration
{

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable("{{%user_accounting_accounts}}",[
            'id'=>$this->primaryKey(),
            'userId'=>$this->integer(),
            'bankName'=>$this->string(),
            'cardNumber'=>$this->string(),
            'accountNumber'=>$this->string(),
            'shaba'=>$this->string(),
            'owner'=>$this->string(),
            'status'=>$this->smallInteger(2),
            'updateTime'=>$this->integer(20),
            'createTime'=>$this->integer(20),
        ], $tableOptions);

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
        ], $tableOptions);

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


        $this->createTable("{{%user_accounting}}",[
            'userId'=>$this->integer(),
            'type'=>$this->smallInteger(2),
            'amount'=>$this->double()->defaultValue(0),
        ], $tableOptions);
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
        $this->dropTable('{{%user_accounting_pay_requests}}');
        $this->dropTable('{{%user_accounting_accounts}}');
    }

}
