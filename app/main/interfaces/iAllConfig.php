<?php


namespace app\main\interfaces;


interface iAllConfig
{
    /**
     * Ant Design Pro 专用配置
     * all-config/adp
     *
     * @return mixed
     */
    public function adp();

    /**
     * 网站设置
     * path: all-config/website
     *
     * @return mixed
     */
    public function website();
}