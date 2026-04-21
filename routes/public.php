<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RegistrationController as PublicRegistrationController;
use Illuminate\Support\Facades\Route;

// Rutas del Catálogo Público de Cursos - Sin middleware web
Route::get('/cursos', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/cursos/{course}', [CatalogController::class, 'show'])->name('catalog.show');

// API endpoints para catálogo
Route::get('/api/courses/availability/{course}', [CatalogController::class, 'checkAvailability'])->name('catalog.availability');
Route::get('/api/courses/search', [CatalogController::class, 'search'])->name('catalog.search');

// Rutas del Carrito de Compras
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/add/{course}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/carrito/remove/{course}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrito/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::put('/carrito/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/invalid', [CartController::class, 'removeInvalid'])->name('cart.remove-invalid');
Route::get('/api/cart/summary', [CartController::class, 'summary'])->name('cart.summary');

// Rutas de Matriculación Pública
Route::get('/matricular', [PublicRegistrationController::class, 'create'])->name('registration.create');
Route::post('/matricular', [PublicRegistrationController::class, 'store'])->name('registration.store');
Route::get('/payment/success', [PublicRegistrationController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PublicRegistrationController::class, 'cancel'])->name('payment.cancel');

// Webhook de Stripe (debe ser accesible públicamente)
Route::post('/stripe/webhook', [PublicRegistrationController::class, 'webhook'])->name('stripe.webhook');

// Test route
Route::get('/test-public', function() {
    return 'Public route works! Time: ' . date('Y-m-d H:i:s');
});
