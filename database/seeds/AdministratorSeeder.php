<?php

use tauthz\facade\Enforcer;
use think\facade\Db;
use think\migration\Seeder;

class AdministratorSeeder extends Seeder
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
        // filestore
        Db::table('filestore')->save([
            'title' => 'null.jpg',
            'owner' => 1,
            'original_title' => 'null.jpg',
            'status' => 1,
            'update_time' => date('Y-m-d H:i:s', time()),
        ]);

        // write admin role
        Enforcer::addPolicy('role:admin', 'domain:main', '*', 'a');
        $authority = new \app\main\model\Authority();
        $authority->save([
            'name' => '超级管理员',
            'title' => '超级管理员',
            'domain' => 1,
            'role' => 1,
            'path' => '*',
            'action' => 'a',
            'description' => '拥有所有权限',
            'status' => 1,
        ]);

        $data = [
            [
                'username' => 'admin',
                'password' => '123456',
                'domain' => 'main',
                'role' => 'admin',
            ],
            [
                'username' => 'unreal',
                'password' => '123456',
                'domain' => 'main',
                'role' => 'admin',
            ],
            [
                'username' => 'youke1',
                'password' => '123456',
                'domain' => 'main',
                'role' => 'guest',
            ],
            [
                'username' => 'youke2',
                'password' => '123456',
                'domain' => 'main',
                'role' => 'guest',
            ],
        ];

        foreach ($data as $value) {
            $domain_id = Db::table('domain')->where('name', $value['domain'])->value('id');
            $role_id = Db::table('role')->where('name', $value['role'])->value('id');
            if ($domain_id && $role_id) {
                Enforcer::addGroupingPolicy(
                    'user:'.$value['username'],
                    'role:'.$value['role'],
                    'domain:'.$value['domain']
                );
                Db::table('administrator')->save([
                    'username' => $value['username'],
                    'ciphertext' => password_hash($this->encryption($value['password']), PASSWORD_DEFAULT),
                    'domain' => $domain_id,
                    'avatar' => 1,
                    'status' => 1,
                    'update_time' => date('Y-m-d H:i:s', time()),
                ]);
                $id = Db::table('administrator')
                    ->where('username', $value['username'])
                    ->value('id');
                Db::table('role_relation')->save([
                    'original' => $role_id,
                    'objective' => $id,
                    'update_time' => date('Y-m-d H:i:s', time()),
                ]);
            }
        }
    }

    /**
     * 管理员专用加密程序
     *
     * 请注意：
     * 你可以自定义加密方法，但此加密函数应与 @see \app\main\controller\Administrator::encryption() 中的加密函数保持一致，
     * 否则将会导致解密失败
     *
     * @param $password
     * @return string
     */
    private function encryption($password)
    {
        $secret_key = Db::name('config')
            ->where('name', 'administrator_secret_key')
            ->value('value');

        // 默认的加密方法
        $e1 = crypt($password, $secret_key);
        $e2 = md5($e1 . $secret_key);
        $e3 = sha1($e2 . $secret_key);
        $e4 = strrev($e3);

        return $e4;
    }
}