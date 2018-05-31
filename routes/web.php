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
Route::get('/getdocumentation',function (){
    $filename = 'grad-project.pdf';
    $path = storage_path($filename);

    return Response::make(file_get_contents($path), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.$filename.'"'
    ]);
});
Route::get('/',function(){
    return view("home");
});