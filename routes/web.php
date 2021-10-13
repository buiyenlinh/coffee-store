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

Route::prefix('admin')->group(function() {
    Route::get('login', 'AdminController@viewLogin')->name('login');
    Route::get('/', 'AdminController@viewHome')->name('home');

    Route::prefix('auth')->group(function() {
        Route::post('login', 'AdminController@login');
    });
});

Route::get('/', function() {
    return view('welcome');
});