<?php

use think\migration\Seeder;

class MainSeeder extends Seeder
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
        // config data
        $config_data = [
            [
                'name' => 'token_secret_key',
                'title' => '令牌加解密密钥',
                'value' => 'o0cKmZCLUQGVtKtlG$zideaK4bLey4%c',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'token_cipher_method',
                'title' => '令牌加密方法',
                'value' => 'AES-128-OFB',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'token_survival_time',
                'title' => '令牌生存时间',
                'value' => 60 * 60 * 12, // 12 hour
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'token_refresh_interval',
                'title' => '令牌刷新间隔时间',
                'value' => 60 * 60 * 24 * 3, // 3 day
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'administrator_secret_key',
                'title' => '管理员密文加解密代码',
                'value' => 'thmkkfXUqX1G&ds0wRubQM7vpmklS6ZF',
                'description' => '专用于管理员密文加解密的密钥',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'member_secret_key',
                'title' => '会员密文加解密代码',
                'value' => 'ifuo%aBxtC2s4%C7KMxY00jmil!3pWhF',
                'description' => '专用于会员密文加解密的密钥',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
        ];
        $this->table('config')->insert($config_data)->save();

        // domain data
        $domain_data = [
            [
                'name' => 'main',
                'title' => '主域',
                'description' => '系统主要的域组设置，请勿修改（若有特殊需求，请在专业人员的指导下修改）',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'merchant',
                'title' => '商户端',
                'description' => '平台下商户的独立域组',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
        ];
        $this->table('domain')->insert($domain_data)->save();

        // role data
        $role_data = [
            [
                'superior' => 0,
                'name' => 'admin',
                'title' => '管理员',
                'domain' => 1,
                'description' => '管理员角色，具备管理系统所有功能和权限。',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'superior' => 0,
                'name' => 'guest',
                'title' => '访客',
                'domain' => 1,
                'description' => '访客角色不具备管理后台功能的权限，只能在一定的范围查看信息',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'superior' => 0,
                'name' => 'merchant',
                'title' => '商户',
                'domain' => 1,
                'description' => '商户角色作为合作身份，具有部分系统权限。',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
        ];
        $this->table('role')->insert($role_data)->save();
    }
}