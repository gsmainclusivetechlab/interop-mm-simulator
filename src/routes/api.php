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

Route::post('/transactions', 'TransactionsController@store')->name('transactions.store');
Route::put('/parties/{type}/{id}', 'TransactionsController@partiesUpdate')->name('parties.update');
Route::get('/parties/{type}/{id}', 'TransactionsController@partiesGet')->name('parties.get');
Route::put('/parties/{type}/{id}/error', 'TransactionsController@partiesError')->name('parties.error');

Route::put('/transactionRequests/{id}', 'TransactionRequestsController@update')->name('transaction_requests.update');
Route::post('/transactionRequests', 'TransactionRequestsController@store')->name('transaction_requests.store');
Route::put('/transactionRequests/{id}/error', 'TransactionRequestsController@error')->name('transaction_requests.error');


Route::post('/transfers', 'TransfersController@store')->name('transfers.store');
Route::put('/transfers/{id}', 'TransfersController@update')->name('transfers.update');
Route::put('/transfers/{id}/error', 'TransfersController@error')->name('transfers.error');

Route::post('/quotations', 'QuotesController@storeQuotations')->name('quotations.store');
Route::put('/quotes/{id}', 'QuotesController@update')->name('quotes.update');
Route::post('/quotes', 'QuotesController@store')->name('quotes.store');
Route::put('/quotes/{id}/error', 'QuotesController@error')->name('quotes.error');
