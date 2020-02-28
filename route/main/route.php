<?php

use think\facade\Route;


// ping
// 目前 Ping 功能主要用于前端。（当前端与服务端无法建立连接时，前端能够定制页面）
Route::any('ping', function () {
    return microtime(true);
});


// common
Route::group('/', function () {
    // Route::any('avatar/store', 'app\common\controller\AvatarStore@store');
})->middleware('authentication');

// config
Route::group('config', function () {
    Route::get('/', 'main/Config/index');
    Route::post('/', 'main/Config/save');
    Route::put('/:id', 'main/Config/update');

    Route::any('/adp', function () {
        return json(['abc' => 'cba']);
        // return 123;
        // return response()->data(123)->code(503);
    });
});

// domain
Route::resource('domain', 'main/Domain');//->middleware('authentication');

// role
Route::resource('role', 'main/Role');//->middleware('authentication');

// authority
Route::resource('authority', 'main/Authority');//->middleware('authentication');



// administrator verification route
Route::any('administrator/verification', 'main/Administrator/verification');
// administrator resource route
Route::resource('administrator', 'main/Administrator');//->middleware('authentication');

