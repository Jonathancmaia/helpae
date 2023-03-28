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

//E-mail verification route
Auth::routes(['verify' => true]);

//Creating Routes
Route::get('/create-service', 'ServiceController@create')->middleware('auth', 'verified')->name('create-service');
Route::get('/create-location', 'LocationController@create')->middleware('auth', 'verified')->name('create-location');

//Suspending Routes
Route::post('/suspend-service', 'ServiceController@suspend')->middleware('auth', 'verified')->name('suspend-service');
Route::post('/suspend-location', 'LocationController@suspend')->middleware('auth', 'verified')->name('suspend-location');

//Storing Routes
Route::post('/store-service', 'ServiceController@store')->middleware('auth', 'verified')->name('store-service');
Route::post('/store-location', 'LocationController@store')->middleware('auth', 'verified')->name('store-location');
Route::get('/panel', 'UserController@edit')->middleware('auth', 'verified')->name('panel');

//Change user Routes
Route::post('/change-name', 'UserController@changeName')->middleware('auth', 'verified')->name('change-name');
Route::post('/change-email', 'UserController@changeEmail')->middleware('auth', 'verified')->name('change-email');
Route::post('/change-password', 'UserController@changePassword')->middleware('auth', 'verified')->name('change-password');
Route::post('/change-cnpj', 'UserController@changeCnpj')->middleware('auth', 'verified')->name('change-cnpj');

//Add pic routes
Route::post('/add_location_pic', 'LocationController@add_pic')->middleware('auth', 'verified')->name('add_location_pic');
Route::post('/add_service_pic', 'ServiceController@add_pic')->middleware('auth', 'verified')->name('add_service_pic');

//Store pic routes
Route::post('/store_location_pic', 'LocationController@store_pic')->middleware('auth', 'verified')->name('store_location_pic');
Route::post('/store_service_pic', 'ServiceController@store_pic')->middleware('auth', 'verified')->name('store_service_pic');

//Delete pic routes
Route::post('/delete_location_pic', 'LocationController@delete_pic')->middleware('auth', 'verified')->name('delete_location_pic');
Route::post('/delete_service_pic', 'ServiceController@delete_pic')->middleware('auth', 'verified')->name('delete_service_pic');

//Delete publication routes
Route::post('/delete-service', 'ServiceController@delete')->middleware('auth', 'verified')->name('delete-service');
Route::post('/delete-location', 'LocationController@delete')->middleware('auth', 'verified')->name('delete-location');

//Message routes
Route::post('/store_message', 'MessageController@store')->middleware('auth', 'verified')->name('store-message');
Route::get('/messages', 'MessageController@index')->middleware('auth', 'verified')->name('messages');
Route::post('/show-messages', 'MessageController@show')->middleware('auth', 'verified')->name('show-messages');

//Showing Routes
Route::get('/show-location/{id}', 'LocationController@show')->name('show-location');
Route::post('/desc-location', 'LocationController@getDesc')->name('desc-location');
Route::get('/show-service/{id}', 'ServiceController@show')->name('show-service');
Route::post('/desc-service', 'ServiceController@getDesc')->name('desc-service');
Route::get('/myAnnounces', function(){
    return view('my-announces', ['id' => Auth::user()->id]);
})->middleware('auth', 'verified')->name('my-announces');
Route::post('/getData-user', 'UserController@getData')->name('getData-user');
Route::get('/show-user/{id}', 'UserController@show')->name('show-user');
Route::post('/show-cities', 'CidadeController@show')->name('show-cities');

//rate user route
Route::post('/rate-user', 'UserController@rate')->middleware('auth', 'verified')->name('rate-user');

//comment user profile route
Route::post('/post-comment', 'UserController@comment')->name('post-comment');

//add user pic route
Route::post('/add-pic', 'UserController@addPic')->name('add-pic');

//Non-Auth routes
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/services', 'ServiceController@index');
Route::get('/locations', 'LocationController@index');

//Turn vip routes
Route::get('/turnVipForm', function(){
    return view('turnVip');
})->name('turnVipForm');

Route::post('/turnVip', 'UserController@turnVip')->middleware('auth', 'verified')->name('turnVip');

Route::get('/terms', function(){ return view('terms'); })->name('terms');

Route::get('/home', 'HomeController@index')->name('home');