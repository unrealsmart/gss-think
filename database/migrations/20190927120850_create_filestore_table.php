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
            'comment' => '标题',
            'null' => false,
        ]);
        $table->addColumn('owner', 'integer', [
            'comment' => '所有者',
            'null' => false,
            'default' => 0,
        ]);
        $table->addColumn('original_title', 'string', [
            'comment' => '原始标题',
            'null' => false,
        ]);
        $table->addColumn('md5', 'string', [
            'comment' => 'MD5',
            'limit' => 64,
            'null' => false,
        ]);
        $table->addColumn('sha1', 'string', [
            'comment' => 'SHA1',
            'limit' => 128,
            'null' => false,
        ]);
        $table->addColumn('path', 'string', [
            'comment' => '路径',
            'null' => false,
        ]);
        $table->addColumn('size', 'integer', [
            'comment' => '容量（单位：bit）',
            'default' => 0,
        ]);
        // 对于文件的权限问题，我们参考了 Linux 系统对文件权限的描述，我们这里使用三位权限
        // 三位权限：所有者（user）、组群（group）、其他人（other）
        // 组群可通过所有者的 租域 + 角色 属性来定位
        $table->addColumn('authority', 'string', [
            'comment' => '权限',
            'limit' => 3,
            'null' => false,
            'default' => '666',
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
