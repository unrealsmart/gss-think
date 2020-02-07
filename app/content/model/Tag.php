<?php
declare (strict_types = 1);

namespace app\content\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Tag extends Model
{
    protected $table = 'tag';

    // 全文搜索请求识别参数名
    public $fs_name = 'fs';

    // 搜索器排除字段
    protected $search_exclude_fields = [
        'id',
        'create_time',
        'update_time',
        'status',
    ];

    public function searchFsAttr($query, $value, $data)
    {
        $expression = [];
        foreach (exclude_search_fields($query, $this->search_exclude_fields) as $name) {
            $expression[] = [$name, 'like', '%' . $value . '%'];
        }
        $query->whereOr($expression);
    }

    public function searchNameAttr($query, $value, $data)
    {
        $query->where('name', $value);
    }

    public function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', $value);
    }

    public function searchStatusAttr($query, $value, $data)
    {
        $query->where('status', $value);
    }
}
