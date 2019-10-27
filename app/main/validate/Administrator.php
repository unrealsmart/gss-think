<?php

namespace app\main\validate;

use think\Validate;

class Administrator extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'id' => 'require',
	    'username' => 'require|length:5,16',
        'password' => 'require|length:5,16',
        'name' => 'max:255',
        'nickname' => 'max:255',
        'gender' => 'number',
        'phone' => 'max:32',
        'email' => 'max:255',
        'status' => 'number',
        'domain' => 'max:255',
        'avatar' => 'number',
        'entrance' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username' => '用户名可能不存在',
        'password' => '用户名或密码错误',
        'entrance' => '未标识的来源',
        'id' => 'ID不存在',
    ];

    /**
     * 定义验证场景
     *
     * @var array
     */
    protected $scene = [
        'verification' => ['username', 'password', 'entrance'],
        'create' => [
            'username', 'password', 'name', 'nickname', 'gender', 'phone', 'email', 'status',
            'domain', 'avatar', 'entrance',
        ],
        'update' => ['username', 'id'],
    ];
}
