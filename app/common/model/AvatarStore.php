<?php


namespace app\common\model;


use app\common\interfaces\iAvatarStoreModel;
use think\Model;

class AvatarStore extends Model implements iAvatarStoreModel
{
    protected $table = 'filestore';
}