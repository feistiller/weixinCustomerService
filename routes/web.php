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

Route::get('/checkToken', 'IndexController@checkToken');
Route::post('/checkToken', 'IndexController@saveChat');
Route::get('/talkList','AdminController@showChats');
Route::get('/talk','AdminController@talk');
Route::get('/showTalk','AdminController@showTalk');
Route::post('/sendMessage','AdminController@sendTalk');
Route::get('/', function () {
    echo "It's worked";
});
