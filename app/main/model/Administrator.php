<?php


namespace app\main\model;


use app\main\interfaces\iAdministratorModel;
use think\Model;

class Administrator extends Model implements iAdministratorModel
{
    protected $table = 'administrator';
}