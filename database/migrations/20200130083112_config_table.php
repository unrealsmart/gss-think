<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ConfigTable extends Migrator
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
        $table = $this->table('config');
        $table->addColumn('name', 'string', [
            'comment' => '名称',
            'limit' => 64,
            'null' => false,
        ]);
        $table->addColumn('title', 'string', [
            'comment' => '标题',
            'null' => false,
        ]);
        $table->addColumn('value', 'string', [
            'comment' => '值',
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
        $table->create();
    }
}
