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

// Route::get('/', [
//      'as' => 'home',
//      function () {
//          return view('welcome');
//      }
//  ]);


// Route::get('/home', function() {
//    return redirect(route('home'));
// });

Route::get('/', 'WelcomeController@index');

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function() {
    Route::resource('users', 'UsersController');
});

Auth::routes();
