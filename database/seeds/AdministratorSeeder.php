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
        // write admin role
        Enforcer::addPolicy('role:admin', 'domain:main', '*', 'a');

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
                    'roles' => $role_id,
                    'status' => 1,
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
        $secret_key = Db::name('global_config')
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