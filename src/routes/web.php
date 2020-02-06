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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/transactions', 'TransactionsController@store')->name('transactions.store');

Route::put('/transactionRequests/{id}', 'TransactionRequestsController@update')->name('transaction_requests.update');


Route::post('/transfers', 'TransfersController@store')->name('transfers.store');
Route::put('/transfers/{id}', 'TransfersController@update')->name('transfers.update');
Route::put('/transfers/{id}/error', 'TransfersController@error')->name('transfers.error');

Route::post('/quotations', 'QuotesController@storeQuotations')->name('quotations.store');
Route::put('/quotes/{id}', 'QuotesController@update')->name('quotes.update');
Route::post('/quotes', 'QuotesController@store')->name('quotes.store');
Route::put('/quotes/{id}/error', 'QuotesController@error')->name('quotes.error');

// TEST
Route::post('/test', 'TestController@post')->name('post-test');

// Logs
Artisan::command('logs:clear', function() {
    exec('rm ' . storage_path('logs/laravel*'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
