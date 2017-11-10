<?php

// 메인 페이지 관리
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\CustomMain\Http\Controllers' ], function() {
    Route::get('custommain', ['as' => 'admin.custommain.index', 'uses' => 'CustomMainController@index']);
    Route::put('custommain', ['as' => 'admin.custommain.update', 'uses' => 'CustomMainController@update']);
});
