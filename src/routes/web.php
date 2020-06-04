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

Route::get('/health', 'HealthCheckController@getStatus');

// Logs
Artisan::command('logs:clear', function() {
    exec('rm ' . storage_path('logs/laravel*'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
