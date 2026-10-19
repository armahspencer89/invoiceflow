<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceEmailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\PaymentController;

// Public homepage
Route::get('/', function () {
    return view('welcome');
});

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Customer routes
    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('customers.index');

    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->name('customers.create');

    Route::post('/customers', [CustomerController::class, 'store'])
        ->name('customers.store');

    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->name('customers.show');

    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->name('customers.edit');

    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->name('customers.update');

    Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])
        ->name('customers.deactivate');

    Route::patch('/customers/{customer}/reactivate', [CustomerController::class, 'reactivate'])
        ->name('customers.reactivate');

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy');

    // Invoice routes
    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index');

    Route::get('/invoices/create', [InvoiceController::class, 'create'])
        ->name('invoices.create');

    Route::post('/invoices', [InvoiceController::class, 'store'])
        ->name('invoices.store');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');

    Route::get('/invoices/{invoice}/pdf', [InvoicePdfController::class, 'download'])
        ->name('invoices.pdf');

    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])
        ->name('invoices.edit');

    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])
        ->name('invoices.update');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
        ->name('invoices.destroy');

    Route::patch('/invoices/{invoice}/send', [InvoiceController::class, 'markAsSent'])
        ->name('invoices.send');

    // Payment routes
    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])
        ->name('invoices.payments.store');

    // Business profile settings
    Route::get('/business-profile', [BusinessProfileController::class, 'edit'])
        ->name('business-profile.edit');

    Route::put('/business-profile', [BusinessProfileController::class, 'update'])
        ->name('business-profile.update');

    //email
    Route::post('/invoices/{invoice}/email', [InvoiceEmailController::class, 'send'])
        ->name('invoices.email');

});
