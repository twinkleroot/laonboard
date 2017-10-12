<?php

// sample 플러그인
Route::get('/test/index', ['as' => 'plugin.test.index', 'uses' => 'TestController@index'] );