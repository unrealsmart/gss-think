<?php

use think\facade\Route;

// wow gold
Route::group('wow-gold', function () {
    // import
    Route::any('import', 'WOWG/import');
    // export
    Route::any('export/:id', 'WOWG/export');
    // date list
    Route::any('dateList', 'WOWG/date_list');
    // static data
    Route::any('static-data', 'WOWG/static_data');
});
