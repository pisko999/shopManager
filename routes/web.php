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
Route::get('/BuyCommand', ['as'=>'buyCommand.index', 'uses' => 'BuyCommandController@index']);
Route::get('/BuyCommand/actual', ['as'=>'buyCommand.actual', 'uses' => 'BuyCommandController@showActual']);
Route::get('/BuyCommand/{id}', ['as'=>'buyCommand.show', 'uses' => 'BuyCommandController@show']);

Route::post('/BuyCommand/{id}/make', ['as'=>'buyCommand.make', 'uses' => 'BuyCommandController@editionMake']);
Route::get('/BuyCommand/{id}/checkQuantity', ['as'=>'buyCommand.checkQuantity', 'uses' => 'BuyCommandConfirmController@checkQuantity']);
Route::post('/BuyCommand/{id}/removeOverQuantity', ['as'=>'buyCommand.removeOverQuantity', 'uses' => 'BuyCommandConfirmController@removeOverQuantity']);
Route::get('/BuyCommand/{id}/close', ['as'=>'buyCommand.close', 'uses' => 'BuyCommandConfirmController@close']);
Route::get('/BuyCommand/{id}/showStocking', ['as'=>'buyCommand.showStocking', 'uses' => 'BuyCommandConfirmController@showStocking']);

Route::get('/BuyCommandEdition', ['as'=>'buyCommandEditionSelect', 'uses' => 'BuyCommandController@editionSelect']);
Route::get('/BuyCommandEditionEdit', ['as'=>'buyCommandEditionGet', 'uses' => 'BuyCommandController@editionGet']);
Route::post('/BuyCommandEditionEdit', ['as'=>'buyCommandEditionSave', 'uses' => 'BuyCommandController@editionSave']);

Route::post('/BuyItemAdd', ['as'=>'buyItem.add', 'uses' => 'BuyItemController@add']);
Route::post('/BuyItemUpdate/{id}', ['as'=>'buyItem.update', 'uses' => 'BuyItemController@update']);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/getMKMStock', ['as' => 'getMKMStock', 'uses' => 'stockController@getMKMStock']);
Route::get('/getMKMStockFile', ['as' => 'getMKMStockFile', 'uses' => 'stockController@getMKMStockFile']);
Route::get('/setStockFromFile', ['as' => 'setStockFromFile', 'uses' => 'stockController@setStockFromFile']);
Route::get('/commands', ['as' => 'commands', 'uses' => 'commandController@getCommands']);
Route::get('/command/{id}', ['as' => 'command', 'uses' => 'commandController@showCommand']);
Route::get('/stockingShow', ['as' => 'stockingShowGet', 'uses' => 'stockController@stockingShowGet']);
Route::post('/stockingShow', ['as' => 'stockingShowPost', 'uses' => 'stockController@stockingShowPost']);
Route::get('/test', ['as' => 'test', 'uses' => 'testController@test']);



Route::get('/stocking', ['as' => 'stockingList', 'uses' => 'StockingController@stockingList']);
Route::get('/stockingEdition', ['as' => 'stockingEditionGet', 'uses' => 'StockingController@stockingEditionGet1']);

Route::post('/admin/stocking', ['as' => 'admin.stocking', 'uses' => 'Admin\StockingController@stockingPost']);
Route::post('/admin/stockingShow', ['as' => 'admin.stockingShow', 'uses' => 'Admin\StockingController@stockingShow']);

