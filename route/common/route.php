<?php

use think\facade\Route;

Route::group('unify', function () {

    // Global Config
    Route::any('config', 'common/unify/config');

    // Login Account
    Route::any('login', 'common/unify/login');

    // Logout Account
    Route::any('logout', 'common/unify/logout');

    // Login Captcha
    Route::any('captcha', 'common/unify/captcha');

    //

});