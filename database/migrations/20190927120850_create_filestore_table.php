<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateFilestoreTable extends Migrator
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
        $table = $this->table('filestore');

        $table->addColumn('title', 'string', [
            'limit' => 255,
            'comment' => '标题',
            'null' => false,
        ]);
        $table->addColumn('owner', 'integer', [
            'limit' => 11,
            'comment' => '所有者',
            'null' => false,
        ]);
        $table->addColumn('original_title', 'string', [
            'limit' => 255,
            'comment' => '原始标题',
            'null' => true,
        ]);
        $table->addColumn('md5', 'string', [
            'limit' => 64,
            'comment' => 'MD5',
            'null' => false,
        ]);
        $table->addColumn('sha1', 'string', [
            'limit' => 128,
            'comment' => 'SHA1',
            'null' => false,
        ]);
        $table->addColumn('path', 'string', [
            'limit' => 255,
            'comment' => '路径',
            'null' => false,
        ]);
        $table->addColumn('size', 'integer', [
            'limit' => 11,
            'comment' => '容量',
            'null' => true,
        ]);
        $table->addColumn('is_public', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'comment' => '公共读写',
            'null' => true,
            'default' => 0,
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
