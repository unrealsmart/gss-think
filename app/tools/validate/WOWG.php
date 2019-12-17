<?php
declare (strict_types = 1);

namespace app\tools\validate;

use think\Validate;

class WOWG extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'file' => [
            'file' => true,
            'fileExt' => 'txt',
            'fileMime' => 'text/plain',
            'fileSize' => 1024 * 1024 * 5,
        ],
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'file.file' => '上传的文件无法识别',
        'file.fileExt' => '不受支持的文件后缀名',
        'file.fileSize' => '上传文件超过文件大小限制',
    ];
}
