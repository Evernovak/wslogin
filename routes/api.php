<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'API\UserController@login');

Route::middleware('auth:api')->group( function () {
    Route::post('register', 'API\UserController@register');
    Route::resource('users', 'API\UserController');
    Route::resource('books', 'API\BookController');
});