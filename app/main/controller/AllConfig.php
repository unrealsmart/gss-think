<?php


namespace app\main\controller;


use app\BaseController;
use app\main\interfaces\iAllConfig;

class AllConfig extends BaseController implements iAllConfig
{

    /**
     * Ant Design Pro 专用配置
     * all-config/adp
     *
     * @return mixed
     */
    public function adp()
    {
        // TODO: Implement adp() method.

        return json([
            'version' => 'alpha.0.0.1',
            'appName' => 'alpha-server',
            'description' => 'this is alpha server. welcome use it.',
            'moreSetting' => [
                'color' => 'blue',
                'theme' => 'default',
                'more' => '...',
            ],
        ]);
    }

    /**
     * 网站设置
     * path: all-config/website
     *
     * @return mixed
     */
    public function website()
    {
        // TODO: Implement website() method.
    }
}