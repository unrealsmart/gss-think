<?php

use think\facade\Route;

Route::any('/re', function (\app\Request $request) {
    $jwt = new \app\common\controller\JsonWebToken();
    dump($jwt->currentUser());
    return 1;
})->middleware('authentication');

Route::any('/er', function () {
    $jwt = new \app\common\controller\JsonWebToken();
    return json($jwt->currentUser());
})->middleware('authentication');


// ping
// 目前 Ping 功能主要用于前端。（当前端与服务端无法建立连接时，前端能够定制页面）
Route::any('ping', function () {
    return microtime(true);
});


// common
Route::group('/', function () {
    // Route::any('avatar/store', 'app\common\controller\AvatarStore@store');
})
    ->middleware('authentication');

// config
Route::group('config', function () {
    Route::get('/', 'main/Config/index');
    Route::post('/', 'main/Config/save');
    Route::put('/:id', 'main/Config/update');
});

// domain
Route::resource('domain', 'main/Domain');
// ->middleware('authentication');

// role
Route::resource('role', 'main/Role');

// authority
Route::resource('authority', 'main/Authority');



// administrator verification route
Route::any('administrator/verification', 'main/Administrator/verification');
// administrator resource route
Route::resource('administrator', 'main/Administrator');
    //->middleware('authentication');

