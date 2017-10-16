<?php

Route::group(['middleware' => 'web', 'prefix' => 'gallery', 'namespace' => 'Modules\Gallery\Http\Controllers'], function()
{
    Route::get('/', 'GalleryController@index');
});
