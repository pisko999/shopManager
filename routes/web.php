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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/getMKMStock', ['as' => 'getMKMStock', 'uses' => 'stockController@getMKMStock']);
Route::get('/getMKMStockFile', ['as' => 'getMKMStockFile', 'uses' => 'stockController@getMKMStockFile']);
Route::get('/setStockFromFile', ['as' => 'setStockFromFile', 'uses' => 'stockController@setStockFromFile']);
Route::get('/commands', ['as' => 'commands', 'uses' => 'commandController@getCommands']);
Route::get('/command/{id}', ['as' => 'command', 'uses' => 'commandController@showCommand']);
Route::get('/stockingShow', ['as' => 'stockingShowGet', 'uses' => 'stockController@stockingShowGet']);
Route::post('/stockingShow', ['as' => 'stockingShowPost', 'uses' => 'stockController@stockingShowPost']);
Route::get('/test', ['as' => 'test', 'uses' => 'testController@test']);

