<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AuthorityTable extends Migrator
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
        $table = $this->table('authority');
        $table->addColumn('title', 'string', [
            'comment' => '标题',
            'null' => false,
        ]);
        $table->addColumn('domain', 'integer', [
            'comment' => '所属租域',
            'null' => false,
            'default' => 0,
        ]);
        $table->addColumn('role', 'integer', [
            'comment' => '所属角色',
            'null' => false,
            'default' => 0,
        ]);
        $table->addColumn('path', 'string', [
            'comment' => '授权路径（支持 Casbin KeyMatch 函数）',
            'null' => false,
        ]);
        $table->addColumn('action', 'string', [
            'comment' => '授权操作',
            'null' => false,
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
        $table->addForeignKey('role', 'role');
        $table->create();
    }
}
