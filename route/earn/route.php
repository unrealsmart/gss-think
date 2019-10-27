<?php

use think\facade\Route;

# hotel
Route::any('hotel/search', 'earn/Hotel/search');
Route::resource('hotel', 'earn/Hotel')->middleware('authentication');

# task
Route::resource('task', 'earn/Task')->middleware('authentication');

