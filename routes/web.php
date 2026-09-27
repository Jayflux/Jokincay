<?php

use App\Http\Controllers\AdminProofDownloadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderExportController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Order Form
Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('throttle:orders')
    ->name('orders.store');

Route::get('/orders/{order}/success', [OrderController::class, 'success'])
    ->name('orders.success');

// Public Customer Tracking
Route::get('/track', [TrackingController::class, 'index'])
    ->name('track');

Route::post('/track/search', [TrackingController::class, 'search'])
    ->middleware('throttle:tracking')
    ->name('track.search');

// Payment Proof Upload
Route::post('/orders/{order}/payment-proof', [PaymentProofController::class, 'store'])
    ->middleware('throttle:payment-upload')
    ->name('orders.payment-proof');

// Secure Admin Proof & Attachment Download/View
Route::get('/admin/payments/{payment}/proof', [AdminProofDownloadController::class, 'show'])
    ->middleware(['web', 'auth'])
    ->name('admin.payments.proof');

Route::get('/admin/orders/{order}/attachment', [AdminProofDownloadController::class, 'downloadOrderAttachment'])
    ->middleware(['web', 'auth'])
    ->name('admin.orders.attachment');

Route::get('/admin/orders/export', [OrderExportController::class, 'export'])
    ->middleware(['web', 'auth'])
    ->name('admin.orders.export');


