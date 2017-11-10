<?php

// 홈페이지 레이아웃 관리
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\CustomLayout\Http\Controllers' ], function() {
    Route::get('customlayout', ['as' => 'admin.customlayout.index', 'uses' => 'CustomLayoutController@index']);
    Route::put('customlayout', ['as' => 'admin.customlayout.update', 'uses' => 'CustomLayoutController@update']);
});
