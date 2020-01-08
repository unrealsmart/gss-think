<?php

namespace app\main\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Domain extends Model
{
    protected $table = 'domain';

    // 搜索器排除字段
    protected $search_exclude_fields = [
        'id',
        'create_time',
        'update_time',
        'status',
    ];

    public $fields = [
        'fs',
    ];

    public function searchFsAttr($query, $value, $data)
    {
        $expression = [];
        foreach (search_fields($query, $this->search_exclude_fields) as $name) {
            $expression[] = [$name, 'like', '%' . $value . '%'];
        }
        $query->whereOr($expression);
    }
}
