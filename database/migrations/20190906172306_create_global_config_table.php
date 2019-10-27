<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

class CreateGlobalConfigTable extends Migrator
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
        $table = $this->table('global_config');

        $table->addColumn('name', 'string', [
            'limit' => 32,
            'comment' => '配置名',
            'null' => false,
        ]);
        $table->addColumn('title', 'string', [
            'limit' => 128,
            'comment' => '配置标题',
            'null' => false,
        ]);
        $table->addColumn('value', 'string', [
            'limit' => 255,
            'comment' => '配置值',
            'null' => false,
        ]);
        $table->addColumn('description', 'string', [
            'limit' => 255,
            'comment' => '描述',
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
