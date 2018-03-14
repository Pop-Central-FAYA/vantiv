<?php

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


Route::get('/welcome', 'Api\DummyController@index');
Route::post('/add-file/{id}', 'Api\DummyController@addFile')->name('api.add.file');

