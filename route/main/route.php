<?php

use think\facade\Route;

// test


// ping
// 目前 Ping 功能主要用于前端。（当前端与服务端无法建立连接时，前端能够定制页面）
Route::any('ping', function () {
    return microtime(true);
});

Route::any('ping-auth', function () {
    return microtime(true);
})
    ->middleware('authentication');


// common
Route::group('/', function () {
    Route::any('avatar/store', 'app\common\controller\AvatarStore@store');

})
    ->middleware('authentication');


// all config
Route::group('all-config', function () {
    Route::any('adp', 'main/AllConfig/adp');
});// ->middleware('authentication');


// administrator verification route
Route::any('administrator/verification', 'main/Administrator/verification');
// administrator resource route
Route::resource('administrator', 'main/Administrator')->middleware('authentication');

// dev
Route::resource('dev', 'main/Dev');

// domain
Route::resource('domain', 'main/Domain');
    // ->middleware('authentication');

// authority
Route::resource('authority', 'main/Authority')->middleware('authentication');


// authentication
Route::any('authentication/test', 'demo/demo')->middleware('authentication');


// authority
