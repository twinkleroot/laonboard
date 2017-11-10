<?php

Route::group(['middleware' => ['web', 'auth', 'admin.menu'], 'prefix' => 'admin', 'namespace' => 'Modules\PopularSearches\Http\Controllers'], function()
{
    // 인기 검색어 관리
    Route::get('popular', ['as' => 'admin.popular.index', 'uses' => 'PopularSearchesController@index']);
    Route::put('popular', ['as' => 'admin.popular.update', 'uses' => 'PopularSearchesController@update']);
    Route::delete('popular', ['as' => 'admin.popular.destroy', 'uses' => 'PopularSearchesController@destroy']);
    // 인기 검색어 순위
    Route::get('popular/rank', ['as' => 'admin.popular.rank', 'uses' => 'PopularSearchesController@rank']);
});
