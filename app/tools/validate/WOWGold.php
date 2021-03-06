<?php
declare (strict_types = 1);

namespace app\tools\validate;

use think\Validate;

class WOWGold extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'file' => [
	        'fileExt' => 'txt,png,jpg',
            'fileSize' => 1024 * 1024 * 5, // 5MB
        ],
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'file.file' => '未接收到文件',
        'file.fileExt' => '不受支持的文件后缀名',
        'file.fileSize' => '文件超过最大上传容量',
    ];
}
