<?php

Route::group(['middleware' => 'web', 'prefix' => 'visit', 'namespace' => 'Modules\Visit\Http\Controllers'], function()
{
    Route::get('/', 'VisitController@index');
});
