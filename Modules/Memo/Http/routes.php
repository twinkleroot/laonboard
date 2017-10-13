<?php

// 쪽지
Route::group(['middleware' => ['web', 'auth', 'valid.user'], 'namespace' => 'Modules\Memo\Http\Controllers'], function()
{
    Route::get('memo/create/{toUser?}', ['as' => 'memo.create', 'uses' => 'MemoController@create']);
    Route::resource('memo', 'MemoController', [
        'except' => [
            'edit', 'update', 'create',
        ],
        'names' => [
            'index' => 'memo.index',
            'show' => 'memo.show',
            'store' => 'memo.store',
            'destroy' => 'memo.destroy',
        ],
    ]);
});
