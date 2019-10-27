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
        // 此函数中使用的 domain = main 需确保数据表 domain 中存在
        $data = [
            [
                'username' => 'admin',
                'password' => '123456',
                'domain' => 'main',
            ],
            [
                'username' => 'unreal',
                'password' => '123456',
                'domain' => 'main',
            ],
        ];

        Enforcer::addPolicy('role:Administrator', 'domain:main', '*', '(.*)', '超管');

        foreach ($data as $value) {
            $ciphertext = password_hash($this->encryption($value['password']), PASSWORD_DEFAULT);

            Enforcer::AddGroupingPolicy('user:'.$value['username'], 'role:Administrator', 'domain:main');

            Db::table('administrator')->save([
                'username' => $value['username'],
                'ciphertext' => $ciphertext,
                'domain' => $value['domain'],
                'status' => 1,
            ]);
        }
    }

    /**
     * 管理员专用加密程序
     *
     * 请注意：
     * 此加密函数应与 main/controller/Administrator.php 中的加密函数保持一致，否则将会导致解密失败
     *
     * @param $password
     * @return string
     */
    private function encryption($password)
    {
        $secret_key = Db::name('global_config')
            ->where('name', 'administrator_secret_key')
            ->value('value');

        $e1 = crypt($password, $secret_key);
        $e2 = md5($e1 . $secret_key);
        $e3 = sha1($e2 . $secret_key);
        $e4 = strrev($e3);

        return $e4;
    }
}