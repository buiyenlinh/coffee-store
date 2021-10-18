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

    Route::prefix('place')->group(function() {
        Route::get('/', 'AdminController@viewPlace')->name('place');
        Route::post('add', 'AdminController@addPlace');
        Route::post('edit', 'AdminController@editPlace');
        Route::post('delete', 'AdminController@deletePlace');
    });

    Route::prefix('category')->group(function() {
        Route::get('/', 'AdminController@viewCategory')->name('category');
        Route::post('add', 'AdminController@addCategory');
        Route::post('edit', 'AdminController@editCategory');
        Route::post('delete', 'AdminController@deleteCategory');
    });

    Route::prefix('table')->group(function() {
        Route::get('/', 'AdminController@viewTable')->name('table');
        Route::post('add', 'AdminController@addTable');
        Route::post('edit', 'AdminController@editTable');
        Route::post('delete', 'AdminController@deleteTable');
    });

    Route::prefix('product')->group(function() {
        Route::get('/', 'AdminController@viewProduct')->name('product');
        Route::post('add', 'AdminController@addProduct');
        Route::post('edit', 'AdminController@editProduct');
        Route::post('delete', 'AdminController@deleteProduct');
    });

    Route::prefix('order')->group(function() {
        Route::get('/', 'AdminController@viewOrder')->name('order');
        Route::get('search-table', 'AdminController@searchTable');
    });
});

Route::get('/', function() {
    return view('welcome');
});