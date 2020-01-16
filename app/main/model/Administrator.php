<?php


namespace app\main\model;


use app\common\model\FileStore;
use think\Model;

class Administrator extends Model
{
    protected $table = 'administrator';

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function avatar()
    {
        return $this->belongsTo(FileStore::class);
    }

    public function getRolesAttr($value)
    {
        return Role::where('id', 'in', explode(',', $value))->select();
    }
}