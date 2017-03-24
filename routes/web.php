<?php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 기본 홈
Route::get('/index', ['as' => 'index', 'uses' => 'WelcomeController@index']);

Route::get('/', 'ThemeController@index');

// 로그인 후 리다이렉트 되는 페이지
Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index'] );

// 인증이 필요한 라우트 그룹
Route::group(['middleware' => 'auth'], function() {
    // 관리자\회원관리에 관련된 CRUD 컨트롤러
    Route::resource('admin/users', 'Admin\UsersController');
    // 리소스 컨트롤러에 더해서 필요한 기능
    Route::put('users/selected_update', ['as' => 'users.selectedUpdate', 'uses' => 'Admin\UsersController@selectedUpdate']);
    // 환경 설정
    Route::get('admin/config', ['as' => 'admin.config', 'uses' => 'Admin\ConfigController@index']);
    Route::put('admin/config/update', ['as' => 'admin.config.update', 'uses' => 'Admin\ConfigController@update']);

    // 사용자가 회원 정보 수정할 때 관련한 라우트들
    Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'User\UserController@edit']);
    Route::put('user/update', ['as' => 'user.update', 'uses' => 'User\UserController@update']);
    Route::get('user/check_password', ['as' => 'user.checkPassword', 'uses' => 'User\UserController@checkPassword']);
    Route::post('user/set_password', ['as' => 'user.setPassword', 'uses' => 'User\UserController@setPassword']);
    Route::post('user/confirm_password', ['as' => 'user.confirmPassword', 'uses' => 'User\UserController@confirmPassword']);
});

// 인증에 관련한 라우트들
Auth::routes();

// 소셜 로그인 - 네이버
Route::get('social/naver', ['as' => 'social.naver', 'uses' => 'Auth\SocialController@redirectToProvider']);
Route::get('social/naver/callback', ['as' => 'social.naver.callback', 'uses' => 'Auth\SocialController@handleProviderCallback']);
Route::post('social/continue', ['as' => 'social.continue', 'uses' => 'Auth\SocialController@continue']);
Route::post('social/connectExistAccount', ['as' => 'social.connectExistAccount', 'uses' => 'Auth\SocialController@connectExistAccount']);

Route::post('register/reCaptcha', ['as' => 'register.reCaptcha', 'uses' => 'Auth\RegisterController@checkRecaptcha']);
