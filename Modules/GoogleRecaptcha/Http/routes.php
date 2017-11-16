<?php

Route::group(['middleware' => 'web', 'prefix' => 'googlerecaptcha', 'namespace' => 'Modules\GoogleRecaptcha\Http\Controllers'], function()
{
    Route::post('', 'GoogleRecaptchaController@googlerecaptcha');
});

// 메인 페이지 관리
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\GoogleRecaptcha\Http\Controllers' ], function() {
    Route::get('googlerecaptcha', ['as' => 'admin.googlerecaptcha.index', 'uses' => 'GoogleRecaptchaController@index']);
    Route::put('googlerecaptcha', ['as' => 'admin.googlerecaptcha.update', 'uses' => 'GoogleRecaptchaController@update']);
});
