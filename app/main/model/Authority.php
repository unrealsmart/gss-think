<?php
declare (strict_types = 1);

namespace app\main\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Authority extends Model
{
    protected $table = 'authority';

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role');
    }

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

    public function searchNameAttr($query, $value, $data)
    {
        $query->where('name', $value);
    }

    public function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', $value);
    }

    public function searchDomainAttr($query, $value, $data)
    {
        $query->where('domain', $value);
    }

    public function searchRoleAttr($query, $value, $data)
    {
        $query->where('role', $value);
    }

    public function searchPathAttr($query, $value, $data)
    {
        $query->where('path', $value);
    }

    public function searchActionAttr($query, $value, $data)
    {
        $query->where('action', $value);
    }
}
