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

    public function searchDomainAttr($query, $value, $data)
    {
        $query->where('domain', $value);
    }
}
