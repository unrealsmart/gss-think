<?php


namespace app\main\interfaces;


interface iAuthentication
{
    /**
     * 登录
     * path: /main/login
     *
     * @return mixed
     */
    public function login();

    /**
     * 注销
     * path: /main/logout
     *
     * @return mixed
     */
    public function logout();

    /**
     * 验证码
     * path: /main/captcha
     *
     * @return mixed
     */
    public function captcha();

    /**
     * 检查
     * path: /main/check
     *
     * @return mixed
     */
    public function check();
}