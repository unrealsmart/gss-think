<?php


namespace app\main\model;


use app\common\model\Filestore;
use think\Model;

class Administrator extends Model
{
    protected $table = 'administrator';

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain');
    }

    public function avatar()
    {
        return $this->belongsTo(Filestore::class, 'avatar');
    }

    public function roles()
    {
        $args = [Role::class, RoleRelation::class, 'original', 'objective'];
        return $this->belongsToMany(...$args);
    }

    public function getRolesAttr($value, $data)
    {
        $data = RoleRelation::where('objective', $data['id'])->column('original', false);
        return array_values($data);
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

    public function searchUsernameAttr($query, $value, $data)
    {
        $query->where('username', $value);
    }

    public function searchDomainAttr($query, $value, $data)
    {
        $query->where('domain', $value);
    }
}