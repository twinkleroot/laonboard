<?php

// 내용 보기
Route::group(['middleware' => 'web', 'prefix' => 'contents', 'namespace' => 'Modules\Content\Http\Controllers'], function()
{
    Route::get('/{contentId}', ['as' => 'content.show', 'uses' => 'ContentsController@show']);
});

// 내용 관리
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\Content\Http\Controllers' ], function() {
    Route::resource('contents', 'ContentsController', [
        'except' => [
            'show'
        ],
        'names' => [
            'index' => 'admin.contents.index',
            'create' => 'admin.contents.create',
            'store' => 'admin.contents.store',
            'edit' => 'admin.contents.edit',
            'update' => 'admin.contents.update',
            'destroy' => 'admin.contents.destroy',
        ],
    ]);
});
