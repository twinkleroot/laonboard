<?php

Route::group(['middleware' => 'web', 'prefix' => 'test1', 'namespace' => 'Modules\Test1\Http\Controllers'], function()
{
    Route::get('/', 'Test1Controller@index');
});
