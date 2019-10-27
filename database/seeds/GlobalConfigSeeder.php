<?php

use think\migration\Seeder;

class GlobalConfigSeeder extends Seeder
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
                'name' => 'entrance',
                'title' => '登录点',
                'value' => 'main',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'jwt_secret_key',
                'title' => 'JSON Web Token 加解密代码',
                'value' => 'o0cKmZCLUQGVtKtlG$zideaK4bLey4%c',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'jwt_cipher_methods',
                'title' => 'JSON Web Token 加密密码学方式',
                'value' => 'AES-128-OFB',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'jwt_last_iat',
                'title' => 'JSON Web Token 最后发布日期',
                'value' => time(),
                'description' => '用于在服务端使用户 JWT 令牌失效',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
            [
                'name' => 'administrator_secret_key',
                'title' => '管理员密文加解密代码',
                'value' => 'thmkkfXUqX1G&ds0wRubQM7vpmklS6ZF',
                'description' => '专用于管理员密文加解密的代码。',
                'status' => 1,
                'update_time' => date('Y-m-d H:i:s', time()),
            ],
        ];

        $global_config = $this->table('global_config');
        $global_config->insert($data)->save();
    }
}