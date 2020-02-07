<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AemRelationTable extends Migrator
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
        $table = $this->table('aem_relation');
        $table->addColumn('original', 'integer', [
            'comment' => '来源ID（模型ID）',
            'null' => false,
        ]);
        $table->addColumn('halfway', 'string', [
            'comment' => '中间名（链接名）',
            'null' => false,
        ]);
        $table->addColumn('objective', 'integer', [
            'comment' => '目标ID（文章ID）',
            'null' => false,
        ]);
        $table->addTimestamps();

        // 注意：如果设置 original 字段的外键时，应该先确定好数据结构
        // 例子（举例不代表真实的数据表）：
        // $table->addForeignKey('objective', 'download'); // 支持多个下载链接
        // $table->addForeignKey('objective', '');

        // 内置的文章扩展数据表，你可以取消一下行的注释，并运行控制台命令生效：
        //

        $table->addForeignKey('objective', 'article');
        $table->create();
    }
}
