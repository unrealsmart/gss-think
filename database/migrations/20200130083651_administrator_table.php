<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdministratorTable extends Migrator
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
        $table = $this->table('administrator');
        $table->addColumn('username', 'string', [
            'comment' => '用户名',
            'limit' => 32,
            'null' => false,
        ]);
        $table->addColumn('ciphertext', 'string', [
            'comment' => '密文', // 经过加密的密文密码
            'limit' => 128,
            'null' => false,
        ]);
        // 用户域不能为空，否则将会导致用户权限全部失效。
        // 无租域也被称之为“死域”或“空域”，正常的新建用户为提交租域ID时，将会返回错误。
        // 当一些特殊条件下，一部分用户将会失去
        $table->addColumn('domain', 'integer', [
            'comment' => '租域',
            'null' => false,
        ]);
        $table->addColumn('email', 'string', [
            'comment' => '邮箱',
        ]);
        $table->addColumn('phone', 'string', [
            'comment' => '手机号码',
            'limit' => 32,
        ]);
        $table->addColumn('nickname', 'string', [
            'comment' => '昵称',
            'limit' => 128,
        ]);
        $table->addColumn('avatar', 'integer', [
            'comment' => '头像',
        ]);
        $table->addColumn('gender', 'integer', [
            'comment' => '性别',
            'limit' => 1,
            'null' => false,
            'default' => 0,
        ]);
        $table->addColumn('description', 'string', [
            'comment' => '描述',
        ]);
        $table->addColumn('status', 'boolean', [
            'comment' => '状态',
            'null' => false,
            'default' => 0,
        ]);
        $table->addTimestamps();
        $table->addForeignKey('domain', 'domain');
        $table->create();
    }
}
