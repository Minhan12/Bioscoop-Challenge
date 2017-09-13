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

Route::get('/', 'SeatController@index');
Route::post('/store', 'SeatController@store')->name('store.seat');
Route::post('/update', 'SeatController@update')->name('update.visitors');