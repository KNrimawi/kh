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

Route::get('/upload','UploadsController@getUpload');
Route::post('/upload','UploadsController@postUpload');
Route::get('download/{token}/{name}','UploadsController@download');
Route::post('/execute','ExecuteController@executeFunction');
Route::get('/execute','ExecuteController@getExecute');