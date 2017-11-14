<?php

Route::group(['middleware' => 'web', 'prefix' => 'recaptcha', 'namespace' => 'Modules\GoogleRecaptcha\Http\Controllers'], function()
{
    Route::post('', 'GoogleRecaptchaController@recaptcha');
});
