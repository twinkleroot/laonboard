<?php

Route::group(['middleware' => ['web', 'auth', 'admin.menu'], 'prefix' => 'admin', 'namespace' => 'Modules\Popup\Http\Controllers'], function()
{
    // 팝업레이어 관리 리소스 컨트롤러
    Route::resource('popup', 'PopupController', [
        'except' => [
            'show',
        ],
        'names' => [
            'index' => 'admin.popup.index',
            'create' => 'admin.popup.create',
            'store' => 'admin.popup.store',
            'edit' => 'admin.popup.edit',
            'update' => 'admin.popup.update',
            'destroy' => 'admin.popup.destroy',
        ]
    ]);
});
