<?php

// sample 플러그인
Route::get('/sample/index', ['as' => 'plugin.sample.index', 'uses' => 'SampleController@index'] );