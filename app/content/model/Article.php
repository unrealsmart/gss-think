<?php
declare (strict_types = 1);

namespace app\content\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Article extends Model
{
    protected $table = 'article';
}
