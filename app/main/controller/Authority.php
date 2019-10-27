<?php


namespace app\main\controller;


use app\BaseController;
use app\common\controller\JsonWebToken;
use app\main\interfaces\iAuthority;
use tauthz\facade\Enforcer;

class Authority extends BaseController implements iAuthority
{
    /**
     * 执行权限验证
     *
     * @param $path
     * @param $action
     * @return mixed
     */
    public static function enforce($path, $action)
    {
        $jwt = new JsonWebToken();
        $currentUser = $jwt->currentUser();

        $sub = 'user:'.$currentUser['username'];
        $dom = 'domain:'.$currentUser['domain'];

        return Enforcer::enforce($sub, $dom, $path, $action, '');
    }

    /**
     * 列表
     *
     * @return mixed
     */
    public function index()
    {
        return json([]);
    }
}