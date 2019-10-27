<?php


namespace app\main\interfaces;


interface iAuthority
{
    /**
     * 执行权限验证
     *
     * @param $path
     * @param $action
     * @return mixed
     */
    public static function enforce($path, $action);

    /**
     * 列表
     *
     * @return mixed
     */
    public function index();
}