<?php


namespace app\main\model;


use app\common\model\FileStore;
use think\Model;

class Administrator extends Model
{
    protected $table = 'administrator';

    // 全文搜索请求识别参数名
    public $fs_name = 'fs';

    // 搜索器排除字段
    protected $search_exclude_fields = [
        'id',
        'create_time',
        'update_time',
        'status',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain');
    }

    public function avatar()
    {
        return $this->belongsTo(FileStore::class, 'avatar');
    }

    public function getRolesAttr($value)
    {
        return Role::where('id', 'in', explode(',', $value))->select();
    }

    public function searchFsAttr($query, $value, $data)
    {
        $expression = [];
        foreach (exclude_search_fields($query, $this->search_exclude_fields) as $name) {
            $expression[] = [$name, 'like', '%' . $value . '%'];
        }
        $query->whereOr($expression);
    }
}