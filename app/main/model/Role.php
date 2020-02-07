<?php
declare (strict_types = 1);

namespace app\main\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Role extends Model
{
    protected $name = 'role';

    public function searchFsAttr($query, $value, $data)
    {
        $exclude_fields = [];
        foreach ($query->getFieldsType() as $k => $v) {
            if (in_array($k, ['id', 'status']) || $v === 'timestamp') {
                $exclude_fields[] = $k;
            }
        }

        $expression = [];
        foreach (exclude_search_fields($query, $exclude_fields) as $name) {
            $expression[] = [$name, 'like', '%' . $value . '%'];
        }

        $query->whereOr($expression);
    }

    public function searchDomainAttr($query, $value, $data)
    {
        $query->where('domain', $value);
    }
}
