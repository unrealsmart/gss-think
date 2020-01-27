<?php

use think\facade\Route;

// store
Route::any('store', 'app\common\controller\FileStore@store');