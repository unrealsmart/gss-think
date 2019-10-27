<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

class CreateAdministratorTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('administrator', [
            'engine' => 'MyISAM',
        ]);

        $table->addColumn('username', 'string', [
            'limit' => 32,
            'comment' => '用户名',
            'null' => false,
        ]);
        $table->addColumn('ciphertext', 'string', [
            'limit' => 128,
            'comment' => '密文',
            'null' => false,
        ]);
        $table->addColumn('domain', 'string', [
            'limit' => 32,
            'comment' => '域',
            'null' => false,
        ]);
        $table->addColumn('email', 'string', [
            'limit' => 64,
            'comment' => '邮箱',
            'null' => true,
        ]);
        $table->addColumn('phone', 'string', [
            'limit' => 11,
            'comment' => '手机号码',
            'null' => true,
        ]);
        $table->addColumn('nickname', 'string', [
            'limit' => 255,
            'comment' => '昵称',
            'null' => true,
        ]);
        $table->addColumn('avatar', 'integer', [
            'limit' => 11,
            'comment' => '头像',
            'null' => true,
        ]);
        $table->addColumn('gender', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '性别',
            'null' => false,
        ]);
        $table->addColumn('status', 'boolean', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '状态',
            'null' => false,
        ]);

        $table->addTimestamps('create_time', 'update_time', true);
        $table->create();
    }
}
