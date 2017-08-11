<?php

// 설치 페이지
Route::get('/install/index', ['as' => 'install.index', 'uses' => 'Install\InstallController@index']);
// 라이센스 확인
Route::get('/install/license', ['as' => 'install.license', 'uses' => 'Install\InstallController@license']);
// 설치 정보 입력
Route::post('/install/form', ['as' => 'install.form', 'uses' => 'Install\InstallController@form']);
// 설치 진행
Route::post('/install/setup', ['as' => 'install.setup', 'uses' => 'Install\InstallController@setup']);
