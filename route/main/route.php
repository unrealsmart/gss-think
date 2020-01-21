<?php

use tauthz\facade\Enforcer;
use think\facade\Route;


Route::any('/t/1', function () {
    return 't/1';
})->middleware('authentication');
Route::any('/t1/1', function () {
    return 't1/1';
})->middleware('authentication');

// test
Route::any('test', function () {

//    dump(lang(null));
//    return 1;

    // Casbin::createRole(['admin']);
    //
    // dump(Enforcer::addPolicy('role:test', 'domain:test', '*', '(.*)', 'TEST'));

    // $casbin = new Casbin();
    // $casbin->addRole();

    // r 读取
    // w 写入
    // d 删除
    // u 更新
    // a 所有

//    dump(Enforcer::addPolicy('role:admin', 'domain:main', '/t*', 'r'));
//    dump(Enforcer::addGroupingPolicy('user:alice', 'role:admin', 'domain:main'));
//    dump(Enforcer::getRolesForUserInDomain('user:alice', 'domain:main'));
//    dump(Enforcer::enforce('user:alice', 'domain:main', '/t/1', 'r'));
//
//    dump(Enforcer::addPolicy('role:admin2', 'domain:main2', '*', 'a'));
//    dump(Enforcer::addGroupingPolicy('user:alice2', 'role:admin2', 'domain:main2'));
//    dump(Enforcer::getRolesForUserInDomain('user:alice2', 'domain:main2'));
//    dump(Enforcer::enforce('user:alice2', 'domain:main2', '/1', 'r'));

//    dump(Enforcer::addPolicy('role:admin', 'domain:main', '/a/*', 'all'));
//    dump(Enforcer::addGroupingPolicy('user:alice', 'role:admin', 'domain:main'));
//    dump(Enforcer::getRolesForUserInDomain('user:admin', 'domain:main'));
//    dump(Enforcer::enforce('user:admin', 'domain:main', '/main/t/*', 'a'));

    return;
});


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

// all config
Route::group('all-config', function () {
    Route::any('adp', 'main/AllConfig/adp');
});// ->middleware('authentication');

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

