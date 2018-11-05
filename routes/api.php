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
Route::get('/all-pushed-files', 'Api\DummyController@allFiles');
Route::post('/add-file/{id}', 'Api\DummyController@addFile')->name('api.add.file');

## playout files
Route::get('/playout-file', 'Api\Playout\FileController@getFiles');
Route::post('/playout-file/started/{file_hash}', 'Api\Playout\FileController@updateDownloadStarted');
Route::post('/playout-file/finished/{file_hash}', 'Api\Playout\FileController@updateDownloadFinished');

## playout lists/adslots/adblocks
Route::get('/playout', 'Api\Playout\PlayoutController@getPlayouts');
Route::post('/playout/placed/{playout_id}', 'Api\Playout\PlayoutController@updateAdPlaced');
Route::post('/playout/played/{playout_id}', 'Api\Playout\PlayoutController@updateAdPlayed');