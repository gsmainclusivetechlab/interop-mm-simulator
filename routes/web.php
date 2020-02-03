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

Route::post('/transactions', 'TransactionsController@store')->name('store-transactions');

Route::put('/transactionRequests/{id}', 'TransactionRequestsController@update')->name('update-transaction-requests');


Route::post('/transfers', 'TransfersController@store')->name('store-transfers');
Route::put('/transfers/{id}', 'TransfersController@update')->name('update-transfers');

// TEST
Route::post('/test', 'TestController@post')->name('post-test');

// Logs
Artisan::command('logs:clear', function() {
    exec('rm ' . storage_path('logs/laravel*'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
