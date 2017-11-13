<?php

Route::group(['middleware' => 'web', 'prefix' => 'googlerecaptcha', 'namespace' => 'Modules\GoogleRecaptcha\Http\Controllers'], function()
{
    Route::get('/', 'GoogleRecaptchaController@index');
});
