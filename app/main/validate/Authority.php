<?php
declare (strict_types = 1);

namespace app\main\validate;

use think\Validate;

class Authority extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'name' => 'require',
        'title' => 'require',
        'domain' => 'require|number',
        'role' => 'require|number',
        'path' => 'require',
        'action' => 'require',
        'status' => 'require|number'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '名称不存在',
        'title.require' => '标题不存在',
        'domain.require' => '租域不存在',
        'domain.number' => '租域格式不正确',
        'role.require' => '角色不存在',
        'role.number' => '角色格式不正确',
        'path.require' => '路径不存在',
        'action.require' => '操作不存在',
        'status.require' => '状态不存在',
        'status.number' => '状态格式不正确',
    ];

    protected $scene = [
        'create' => ['name', 'title', 'domain', 'role', 'path', 'action', 'status'],
        'update' => ['name', 'title', 'path', 'action', 'status'],
    ];
}
