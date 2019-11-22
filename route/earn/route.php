<?php

use think\facade\Route;

# hotel
Route::resource('hotel', 'earn/Hotel')->middleware('authentication');

# room
Route::resource('room', 'earn/Room')->middleware('authentication');

# date-price
Route::resource('date-price', 'earn/datePrice')->middleware('authentication');

# task
Route::any('run-crawl-task', 'earn/Task/runCrawlTask')->middleware('authentication');
Route::resource('task', 'earn/Task')->middleware('authentication');

