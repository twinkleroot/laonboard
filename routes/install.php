<?php

// 설치 페이지
Route::get('/install/index', ['as' => 'install.index', 'uses' => 'InstallController@index']);
// 라이센스 확인
Route::get('/install/license', ['as' => 'install.license', 'uses' => 'InstallController@license']);
// 라이센스 체크 검사
Route::post('/install/license', ['as' => 'install.license.check', 'uses' => 'InstallController@licenseCheck']);
// 설치 정보 입력
Route::get('/install/form', ['as' => 'install.form', 'uses' => 'InstallController@form']);
// 설치 진행
Route::post('/install/setup', ['as' => 'install.setup', 'uses' => 'InstallController@setup']);
