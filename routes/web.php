<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

//Customer route

Route::get('/customers', [CustomerController::class, 'index'])
    ->middleware('auth')
    ->name('customers.index');
Route::get('/customers/create', [CustomerController::class, 'create'])
    ->middleware('auth')
    ->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])
    ->middleware('auth')
    ->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])
    ->middleware('auth')
    ->name('customers.show');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
    ->middleware('auth')
    ->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])
    ->middleware('auth')
    ->name('customers.update');
Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])
    ->middleware('auth')
    ->name('customers.deactivate');
Route::patch('/customers/{customer}/reactivate', [CustomerController::class, 'reactivate'])
    ->middleware('auth')
    ->name('customers.reactivate');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
    ->middleware('auth')
    ->name('customers.destroy');
