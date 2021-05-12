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
Route::get('/BuyCommand', ['as' => 'buyCommand.index', 'uses' => 'BuyCommandController@index']);
Route::get('/BuyCommand/actual', ['as' => 'buyCommand.actual', 'uses' => 'BuyCommandController@showActual']);
Route::get('/BuyCommand/{id}', ['as' => 'buyCommand.show', 'uses' => 'BuyCommandController@show']);

Route::post('/BuyCommand/{id}/make', ['as' => 'buyCommand.make', 'uses' => 'BuyCommandController@editionMake']);
Route::get('/BuyCommand/{id}/checkQuantity', ['as' => 'buyCommand.checkQuantity', 'uses' => 'BuyCommandConfirmController@checkQuantity']);
Route::post('/BuyCommand/{id}/removeOverQuantity', ['as' => 'buyCommand.removeOverQuantity', 'uses' => 'BuyCommandConfirmController@removeOverQuantity']);
Route::get('/BuyCommand/{id}/close', ['as' => 'buyCommand.close', 'uses' => 'BuyCommandConfirmController@close']);
Route::get('/BuyCommand/{id}/showStocking', ['as' => 'buyCommand.showStocking', 'uses' => 'BuyCommandConfirmController@showStocking']);

Route::get('/BuyCommandEdition', ['as' => 'buyCommandEditionSelect', 'uses' => 'BuyCommandController@editionSelect']);
Route::get('/BuyCommandEditionEdit', ['as' => 'buyCommandEditionGet', 'uses' => 'BuyCommandController@editionGet']);
Route::post('/BuyCommandEditionEdit', ['as' => 'buyCommandEditionSave', 'uses' => 'BuyCommandController@editionSave']);

Route::post('/BuyItemAdd', ['as' => 'buyItem.add', 'uses' => 'BuyItemController@add']);
Route::post('/BuyItemUpdate/{id}', ['as' => 'buyItem.update', 'uses' => 'BuyItemController@update']);
Route::post('/BuyItem/{id}/updateState/{state}', ['as' => 'buyItem.updateState', 'uses' => 'BuyItemController@updateState']);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/getMKMStock', ['as' => 'getMKMStock', 'uses' => 'stockController@getMKMStock']);
Route::get('/getMKMStockFile', ['as' => 'getMKMStockFile', 'uses' => 'stockController@getMKMStockFile']);
Route::get('/setStockFromFile', ['as' => 'setStockFromFile', 'uses' => 'stockController@setStockFromFile']);
Route::get('/commands', ['as' => 'commands', 'uses' => 'commandController@getCommands']);
Route::get('/command/type', ['as' => 'commandShowByType', 'uses' => 'commandController@showCommandsByType']);
Route::post('/command/trackingNumber', ['as' => 'command.trackingNumber', 'uses' => 'commandController@trackingNumber']);
Route::get('/command/sendAll', ['as' => 'command.sendAll', 'uses' => 'commandController@sendAll']);
Route::post('/command/checkMKM', ['as' => 'command.checkMKM', 'uses' => 'commandController@checkMKM']);
Route::get('/command/set/paid/{id}', ['as' => 'commandSetPaid', 'uses' => 'commandController@setPaid']);
Route::get('/command/set/send/{id}', ['as' => 'commandSetSend', 'uses' => 'commandController@setSend']);
Route::get('/command/acceptCancellation/{id}/{relistItems}', ['as' => 'commandAcceptCancellation', 'uses' => 'commandController@acceptCancellation']);
Route::get('/command/{id}/printable', ['as' => 'commandShowPrintable', 'uses' => 'commandController@showPrintableCommand']);
Route::get('/command/{id}', ['as' => 'command', 'uses' => 'commandController@showCommand']);
Route::get('/Addresses', ['as' => 'commandAddresses', 'uses' => 'commandController@printAddresses']);
Route::get('/Addresses/SetPosition/{position}', ['as' => 'commandAddresses.setPosition', 'uses' => 'commandController@setPosition']);
Route::get('/commandAddress/{id}', ['as' => 'commandAddress', 'uses' => 'commandController@printAddress']);
Route::get('/commandFacture/{id}', ['as' => 'commandFacture', 'uses' => 'commandController@printFacture']);
Route::get('/commands/printPaid', ['as' => 'commandPrintPaid', 'uses' => 'commandController@printPaidFactures']);
Route::get('/stockingShow', ['as' => 'stockingShowGet', 'uses' => 'stockController@stockingShowGet']);
Route::post('/stockingShow', ['as' => 'stockingShowPost', 'uses' => 'stockController@stockingShowPost']);
Route::get('/test', ['as' => 'test', 'uses' => 'testController@test']);

Route::get('/stockEditSelect', ['as' => 'stockEditSelect', 'uses' => 'stockController@stockEditSelect']);
Route::get('/stockEditGet', ['as' => 'stockEditGet', 'uses' => 'stockController@stockEditGet']);
Route::post('/stock/{id}/UpdateQuantity', ['as' => 'stockUpdateQuantity', 'uses' => 'stockController@stockUpdateQuantity']);


Route::get('/stocking', ['as' => 'stockingList', 'uses' => 'StockingController@stockingList']);
Route::get('/stockingEdition', ['as' => 'stockingEditionGet', 'uses' => 'StockingController@stockingEditionGet1']);

Route::post('/admin/stocking', ['as' => 'admin.stocking', 'uses' => 'Admin\StockingController@stockingPost']);
Route::post('/admin/stockingShow', ['as' => 'admin.stockingShow', 'uses' => 'Admin\StockingController@stockingShow']);

Route::get('/testPDF', ['as' => 'testPdf', 'uses' => 'testController@testPdf']);

Route::post('/Deck/Card/Add', ['as' => 'deck.card.add', 'uses' => 'CardDeckController@addCard']);
Route::post('/Deck/Card/increase/{id}', ['as' => 'deck.card.increase', 'uses' => 'CardDeckController@increase']);
Route::post('/Deck/Card/decrease/{id}', ['as' => 'deck.card.decrease', 'uses' => 'CardDeckController@decrease']);
Route::post('/Deck/Card/remove/{id}', ['as' => 'deck.card.remove', 'uses' => 'CardDeckController@remove']);

Route::get('/Deck', ['as' => 'deck.index', 'uses' => 'DeckController@index']);
Route::post('/Deck/Create', ['as' => 'deck.create', 'uses' => 'DeckController@create']);
Route::get('/Deck/Check/{id}', ['as' => 'deck.check', 'uses' => 'DeckController@check']);
Route::get('/Deck/{id}', ['as' => 'deck.show', 'uses' => 'DeckController@show']);

