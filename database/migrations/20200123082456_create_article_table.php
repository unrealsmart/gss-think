<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateArticleTable extends Migrator
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
        $table = $this->table('article');
        $table->addColumn('title', 'string', [
            'comment' => '标题',
            'null' => false,
        ]);
        $table->addColumn('subtitle', 'string', [
            'comment' => '副标题/子标题',
            'null' => false,
        ]);
        $table->addColumn('cover', 'integer', [
            'comment' => '封面',
        ]);
        // 分类不推荐实施交叉功能
        $table->addColumn('category', 'integer', [
            'comment' => '分类',
            'null' => false,
        ]);
        $table->addColumn('content', 'text', [
            'comment' => '内容',
        ]);
        $table->addColumn('tags', 'string', [
            'comment' => '标签',
        ]);
        // 模型对应多个内容扩展数据表
        $table->addColumn('models', 'string', [
            'comment' => '模型',
        ]);
        $table->addColumn('status', 'boolean', [
            'comment' => '状态',
            'null' => false,
            'default' => 0,
        ]);
        $table->addColumn('release_time', 'datetime', [
            'comment' => '发布时间',
        ]);
        $table->addTimestamps();
        $table->create();
    }
}
