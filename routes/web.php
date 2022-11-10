<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Creating Routes
Route::get('/create-service', 'ServiceController@create')->middleware('auth')->name('create-service');
Route::get('/create-location', 'LocationController@create')->middleware('auth')->name('create-location');

//Storing Routes
Route::post('/store-service', 'ServiceController@store')->middleware('auth')->name('store-service');
Route::post('/store-location', 'LocationController@store')->middleware('auth')->name('store-location');
Route::get('/panel', 'UserController@edit')->middleware('auth')->name('panel');

//Change user Routes
Route::post('/change-name', 'UserController@changeName')->middleware('auth')->name('change-name');
Route::post('/change-email', 'UserController@changeEmail')->middleware('auth')->name('change-email');
Route::post('/change-password', 'UserController@changePassword')->middleware('auth')->name('change-password');

//Add pic routes
Route::post('/add_location_pic', 'LocationController@add_pic')->middleware('auth')->name('add_location_pic');
Route::post('/add_service_pic', 'ServiceController@add_pic')->middleware('auth')->name('add_service_pic');

//Store pic routes
Route::post('/store_location_pic', 'LocationController@store_pic')->middleware('auth')->name('store_location_pic');
Route::post('/store_service_pic', 'ServiceController@store_pic')->middleware('auth')->name('store_service_pic');

//Delete pic routes
Route::post('/delete_location_pic', 'LocationController@delete_pic')->middleware('auth')->name('delete_location_pic');
Route::post('/delete_service_pic', 'ServiceController@delete_pic')->middleware('auth')->name('delete_service_pic');

//Message routes
Route::post('/store_message', 'MessageController@store')->middleware('auth')->name('store-message');
Route::get('/messages', 'MessageController@index')->name('messages');
Route::post('/show-messages', 'MessageController@show')->name('show-messages');

//Showing Routes
Route::get('/show-location/{id}', 'LocationController@show')->name('show-location');
Route::post('/desc-location', 'LocationController@getDesc')->name('desc-location');
Route::get('/show-service/{id}', 'ServiceController@show')->name('show-service');
Route::post('/desc-service', 'ServiceController@getDesc')->name('desc-service');
Route::get('/myAnnounces', function(){
    return view('my-announces', ['id' => Auth::user()->id]);
})->middleware('auth')->name('my-announces');
Route::post('/getData-user', 'UserController@getData')->name('getData-user');

//Non-Auth routes
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/services', 'ServiceController@index');
Route::get('/locations', 'LocationController@index');

//Turn vip routes
Route::get('/turnVipForm', function(){
    return view('turnVip');
})->name('turnVipForm');

Route::post('/turnVip', 'UserController@turnVip')->middleware('auth')->name('turnVip');;

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Payment status change
Route::post('/paymentReturn', 'UserController@paymentReturn');