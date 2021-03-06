<?php

use think\facade\Route;

// category
Route::resource('category', 'Content/Category');
    //->middleware('authentication');

// tag
Route::resource('tag', 'Content/Tag');

// AEM（article extended model）
Route::any('aem', 'Content/Aem');

// article
Route::resource('article', 'Content/Article');
