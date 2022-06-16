<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\CommandController;

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
Route::get('/BuyCommand', ['as' => 'buyCommand.index', 'uses' => 'App\Http\Controllers\BuyCommandController@index']);
Route::get('/BuyCommand/actual', ['as' => 'buyCommand.actual', 'uses' => 'App\Http\Controllers\BuyCommandController@showActual']);
Route::get('/BuyCommand/{id}', ['as' => 'buyCommand.show', 'uses' => 'App\Http\Controllers\BuyCommandController@show']);

Route::post('/BuyCommand/{id}/make', ['as' => 'buyCommand.make', 'uses' => 'App\Http\Controllers\BuyCommandController@editionMake']);
Route::get('/BuyCommand/{id}/checkQuantity', ['as' => 'buyCommand.checkQuantity', 'uses' => 'App\Http\Controllers\BuyCommandConfirmController@checkQuantity']);
Route::post('/BuyCommand/{id}/removeOverQuantity', ['as' => 'buyCommand.removeOverQuantity', 'uses' => 'App\Http\Controllers\BuyCommandConfirmController@removeOverQuantity']);
Route::get('/BuyCommand/{id}/close', ['as' => 'buyCommand.close', 'uses' => 'App\Http\Controllers\BuyCommandConfirmController@close']);
Route::get('/BuyCommand/{id}/showStocking', ['as' => 'buyCommand.showStocking', 'uses' => 'App\Http\Controllers\BuyCommandConfirmController@showStocking']);

Route::get('/BuyCommandEdition', ['as' => 'buyCommandEditionSelect', 'uses' => 'App\Http\Controllers\BuyCommandController@editionSelect']);
Route::get('/BuyCommandEditionEdit', ['as' => 'buyCommandEditionGet', 'uses' => 'App\Http\Controllers\BuyCommandController@editionGet']);
Route::post('/BuyCommandEditionEdit', ['as' => 'buyCommandEditionSave', 'uses' => 'App\Http\Controllers\BuyCommandController@editionSave']);

Route::post('/BuyItemAdd', ['as' => 'buyItem.add', 'uses' => 'App\Http\Controllers\BuyItemController@add']);
Route::post('/BuyItemUpdate/{id}', ['as' => 'buyItem.update', 'uses' => 'App\Http\Controllers\BuyItemController@update']);
Route::post('/BuyItem/{id}/updateState/{state}', ['as' => 'buyItem.updateState', 'uses' => 'App\Http\Controllers\BuyItemController@updateState']);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/getMKMStock', ['as' => 'getMKMStock', 'uses' => 'App\Http\Controllers\stockController@getMKMStock']);
Route::get('/getMKMStockFile', ['as' => 'getMKMStockFile', 'uses' => 'App\Http\Controllers\stockController@getMKMStockFile']);
Route::get('/setStockFromFile', ['as' => 'setStockFromFile', 'uses' => 'App\Http\Controllers\stockController@setStockFromFile']);
Route::get('/commands', ['as' => 'commands', 'uses' => 'App\Http\Controllers\CommandController@getCommands']);
Route::get('/command/type', ['as' => 'commandShowByType', 'uses' => 'App\Http\Controllers\CommandController@showCommandsByType']);
Route::post('/command/trackingNumber', ['as' => 'command.trackingNumber', 'uses' => 'App\Http\Controllers\CommandController@trackingNumber']);
Route::get('/command/sendAll', ['as' => 'command.sendAll', 'uses' => 'App\Http\Controllers\CommandController@sendAll']);
Route::get('/command/action', ['as' => 'command.action', 'uses' => 'App\Http\Controllers\CommandController@action']);
Route::post('/command/checkMKM', ['as' => 'command.checkMKM', 'uses' => 'App\Http\Controllers\CommandController@checkMKM']);
Route::get('/command/set/paid/{id}', ['as' => 'commandSetPaid', 'uses' => 'App\Http\Controllers\CommandController@setPaid']);
Route::get('/command/set/send/{id}', ['as' => 'commandSetSend', 'uses' => 'App\Http\Controllers\CommandController@setSend']);
Route::get('/command/acceptCancellation/{id}/{relistItems}', ['as' => 'commandAcceptCancellation', 'uses' => 'App\Http\Controllers\CommandController@acceptCancellation']);
Route::get('/command/{id}/printable', ['as' => 'commandShowPrintable', 'uses' => 'App\Http\Controllers\CommandController@showPrintableCommand']);
Route::get('/command/{id}', ['as' => 'command', 'uses' => 'App\Http\Controllers\CommandController@showCommand']);
Route::get('/Addresses', ['as' => 'commandAddresses', 'uses' => 'App\Http\Controllers\CommandController@printAddresses']);
Route::get('/Addresses/SetPosition/{position}', ['as' => 'commandAddresses.setPosition', 'uses' => 'App\Http\Controllers\CommandController@setPosition']);
Route::get('/commandAddress/{id}', ['as' => 'commandAddress', 'uses' => 'App\Http\Controllers\CommandController@printAddress']);
Route::get('/commandFacture/{id}', ['as' => 'commandFacture', 'uses' => 'App\Http\Controllers\CommandController@printFacture']);
Route::get('/commands/printPaid', ['as' => 'commandPrintPaid', 'uses' => 'App\Http\Controllers\CommandController@printPaidFactures']);
Route::get('/stockingShow', ['as' => 'stockingShowGet', 'uses' => 'App\Http\Controllers\stockController@stockingShowGet']);
Route::post('/stockingShow', ['as' => 'stockingShowPost', 'uses' => 'App\Http\Controllers\stockController@stockingShowPost']);
Route::get('/test', ['as' => 'test', 'uses' => 'App\Http\Controllers\testController@test']);

Route::get('/stockEditSelect', ['as' => 'stockEditSelect', 'uses' => 'App\Http\Controllers\stockController@stockEditSelect']);
Route::get('/stockEditGet', ['as' => 'stockEditGet', 'uses' => 'App\Http\Controllers\stockController@stockEditGet']);
Route::post('/stock/{id}/UpdateQuantity', ['as' => 'stockUpdateQuantity', 'uses' => 'App\Http\Controllers\stockController@stockUpdateQuantity']);


Route::get('/stocking', ['as' => 'stockingList', 'uses' => 'App\Http\Controllers\StockingController@stockingList']);
Route::get('/stockingEdition', ['as' => 'stockingEditionGet', 'uses' => 'App\Http\Controllers\StockingController@stockingEditionGet1']);

Route::post('/admin/stocking', ['as' => 'admin.stocking', 'uses' => 'App\Http\Controllers\Admin\StockingController@stockingPost']);
Route::post('/admin/stockingShow', ['as' => 'admin.stockingShow', 'uses' => 'App\Http\Controllers\Admin\StockingController@stockingShow']);

Route::get('/testPDF', ['as' => 'testPdf', 'uses' => 'App\Http\Controllers\testController@testPdf']);

Route::post('/Deck/Card/Add', ['as' => 'deck.card.add', 'uses' => 'App\Http\Controllers\CardDeckController@addCard']);
Route::post('/Deck/Card/increase/{id}', ['as' => 'deck.card.increase', 'uses' => 'App\Http\Controllers\CardDeckController@increase']);
Route::post('/Deck/Card/decrease/{id}', ['as' => 'deck.card.decrease', 'uses' => 'App\Http\Controllers\CardDeckController@decrease']);
Route::post('/Deck/Card/remove/{id}', ['as' => 'deck.card.remove', 'uses' => 'App\Http\Controllers\CardDeckController@remove']);

Route::get('/Deck', ['as' => 'deck.index', 'uses' => 'App\Http\Controllers\DeckController@index']);
Route::post('/Deck/Create', ['as' => 'deck.create', 'uses' => 'App\Http\Controllers\DeckController@create']);
Route::get('/Deck/Check/{id}', ['as' => 'deck.check', 'uses' => 'App\Http\Controllers\DeckController@check']);
Route::get('/Deck/{id}', ['as' => 'deck.show', 'uses' => 'App\Http\Controllers\DeckController@show']);

Route::get('/shopping/{id}', ['as' => 'shopping.show', 'uses' => 'App\Http\Controllers\RedirectController@shopping']);
Route::get('/info', function (){return phpinfo();});

Route::get('/expansions', function () {return view('expansion.index'); })->name('expansion.index');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
