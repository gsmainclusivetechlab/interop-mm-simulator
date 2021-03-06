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

Route::post('/transactions', 'TransactionsController@store')->name('transactions.store');

Route::put('/transactionRequests/{id}', 'TransactionRequestsController@update')->name('transaction_requests.update');
Route::put('/transactionRequests/{id}/error', 'TransactionRequestsController@error')->name('transaction_requests.error');

Route::put('/quotes/{id}', 'QuotesController@update')->name('quotes.update');
Route::post('/quotes', 'QuotesController@store')->name('quotes.store');
Route::put('/quotes/{id}/error', 'QuotesController@error')->name('quotes.error');

Route::post('/transfers', 'TransfersController@store')->name('transfers.store');
Route::put('/transfers/{id}', 'TransfersController@update')->name('transfers.update');
Route::put('/transfers/{id}/error', 'TransfersController@error')->name('transfers.error');

Route::get('/authorizations/{id}', 'AuthorizationsController@show')->name('authorizations.show');

Route::put('/participants/{type}/{id}', 'ParticipantsController@update')->name('participants.update');
Route::put('/participants/{type}/{id}/error', 'ParticipantsController@error')->name('participants.error');
