<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'inform', 'namespace' => 'Modules\Inform\Http\Controllers'], function()
{
    // 회원 알림 내역
    Route::get('', ['as' => 'inform', 'uses' => 'InformController@index']);
    // 회원 알림 읽음 표시
    Route::put('', ['as' => 'inform.markAsRead', 'uses' => 'InformController@markAsRead']);
    // 회원 알림 내역 삭제
    Route::delete('', ['as' => 'inform.destroy', 'uses' => 'InformController@destroy']);
    // ajax - 회원 알림 읽음 표시
    Route::put('markone', ['as' => 'inform.markAsReadOne', 'uses' => 'InformController@markAsReadOne']);
});

// 알림 설정
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\Inform\Http\Controllers' ], function() {
    Route::get('inform', ['as' => 'admin.inform.index', 'uses' => 'InformController@adminIndex']);
    Route::put('inform', ['as' => 'admin.inform.update', 'uses' => 'InformController@adminUpdate']);
});
