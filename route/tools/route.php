<?php

use think\facade\Route;

// wow gold
Route::group('wow-gold', function () {
    // import
    Route::any('import', 'WOWG/import');
    // date list
    Route::any('dateList', 'WOWG/date_list');
    // export
    Route::any('export/:id', 'WOWG/export');
});
