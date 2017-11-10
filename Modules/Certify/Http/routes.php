<?php

Route::group(['middleware' => 'web', 'prefix' => 'certify', 'namespace' => 'Modules\Certify\Http\Controllers'], function()
{
    // KCB 본인 확인 서비스
    Route::get('kcb/hpcert1', ['as' => 'certify.kcb.hp1', 'uses' => 'CertifyController@kcbHpCert1']);
    Route::post('kcb/hpcert2', ['as' => 'certify.kcb.hp2', 'uses' => 'CertifyController@kcbHpCert2']);

    // 회원 가입전 본인확인 여부 검사
    Route::post('validate', ['as' => 'ajax.validate.cert', 'uses' => 'CertifyController@validateCertBeforeJoin']);
    // 같은 사람의 본인확인 데이터를 사용했는지 검사
    Route::post('exist', ['as' => 'ajax.exist.cert', 'uses' => 'CertifyController@existCertData']);
    // 사용자 데이터에 본인확인 데이터를 포함시킨다.
    Route::post('merge', ['as' => 'ajax.merge.cert', 'uses' => 'CertifyController@mergeUserData']);
});

// 본인 인증 설정
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'admin.menu'], 'namespace' => 'Modules\Certify\Http\Controllers' ], function() {
    Route::get('certify', ['as' => 'admin.certify.index', 'uses' => 'CertifyController@index']);
    Route::put('certify', ['as' => 'admin.certify.update', 'uses' => 'CertifyController@update']);
});
