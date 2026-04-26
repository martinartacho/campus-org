<?php

use App\Http\Controllers\RegistrationController as PublicRegistrationController;
use Illuminate\Support\Facades\Route;

// Webhook de Stripe - SIN CSRF
Route::post('/stripe/webhook', [PublicRegistrationController::class, 'webhook'])->name('stripe.webhook');
