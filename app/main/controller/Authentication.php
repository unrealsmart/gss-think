<?php


namespace app\main\controller;


use app\BaseController;
use app\main\interfaces\iAuthentication;

class Authentication extends BaseController implements iAuthentication
{

    /**
     * 登录
     * path: /main/login
     *
     * @return mixed
     */
    public function login()
    {
        // TODO: Implement login() method.
    }

    /**
     * 注销
     * path: /main/logout
     *
     * @return mixed
     */
    public function logout()
    {
        // TODO: Implement logout() method.
    }

    /**
     * 验证码
     * path: /main/captcha
     *
     * @return mixed
     */
    public function captcha()
    {
        // TODO: Implement captcha() method.
    }

    /**
     * 检查
     * path: /main/check
     *
     * @return mixed
     */
    public function check()
    {
        // TODO: Implement check() method.
    }
}