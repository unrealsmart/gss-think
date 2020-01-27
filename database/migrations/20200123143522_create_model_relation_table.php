<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateModelRelationTable extends Migrator
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
        $table = $this->table('model_relation');
        $table->addColumn('original', 'integer', [
            'comment' => '关系源ID',
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'comment' => '扩展模型名称',
            'null' => false,
        ]);
        $table->addColumn('objective', 'integer', [
            'comment' => '目标ID',
            'null' => false,
        ]);
        $table->addTimestamps();
        $table->create();
    }
}
