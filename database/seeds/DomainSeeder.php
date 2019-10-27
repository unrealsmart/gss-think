<?php

use think\migration\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'name' => 'main',
                'title' => '主域',
                'description' => '系统主要的域组设置，请勿修改（若有特殊需求，请在专业人员的指导下修改）',
                'status' => 1,
            ],
            [
                'name' => 'merchant',
                'title' => '商户端',
                'description' => '平台下商户的独立域组',
                'status' => 1,
            ],
        ];

        // 添加域组
        $domain = $this->table('domain');
        $domain->insert($data)->save();
    }
}