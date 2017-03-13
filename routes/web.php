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
    Route::put('users/selected_update',
        ['as' => 'users.selectedUpdate', 'uses' => 'Admin\UsersController@selectedUpdate']);
    Route::resource('users', 'Admin\UsersController');
});

Auth::routes();

Auth::routes();

Route::get('/home', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');
