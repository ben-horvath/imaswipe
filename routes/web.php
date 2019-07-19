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

Route::get('/', 'WelcomeController@show')->name('welcome');

Route::get('assess', 'AssessController@show')->middleware('auth');

Route::get('merge', 'MergeController@show')->middleware('auth');

Route::get('home', 'HomeController@index')->name('home');

Route::post('upgrade', 'UserController@upgrade')->name('upgrade');

Auth::routes();

Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('{name}', 'WelcomeController@startWith')->name('welcome-start-with');
