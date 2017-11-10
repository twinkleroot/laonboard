<?php

// 내용 보기
Route::group(['middleware' => 'web', 'prefix' => 'content', 'namespace' => 'Modules\Content\Http\Controllers'], function()
{
    Route::get('/{contentId}', ['as' => 'content.show', 'uses' => 'ContentController@show']);
});

// 내용 관리
Route::group(['middleware' => ['web', 'auth', 'admin.menu'], 'prefix' => 'admin', 'namespace' => 'Modules\Content\Http\Controllers' ], function() {
    Route::resource('content', 'ContentController', [
        'except' => [
            'show'
        ],
        'names' => [
            'index' => 'admin.content.index',
            'create' => 'admin.content.create',
            'store' => 'admin.content.store',
            'edit' => 'admin.content.edit',
            'update' => 'admin.content.update',
            'destroy' => 'admin.content.destroy',
        ],
    ]);
});
