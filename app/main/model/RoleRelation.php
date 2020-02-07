<?php
declare (strict_types = 1);

namespace app\main\model;

use think\model\Pivot;

/**
 * @mixin think\Pivot
 */
class RoleRelation extends Pivot
{
    protected $name = 'role_relation';
}
