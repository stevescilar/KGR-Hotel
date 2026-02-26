<?php

// routes/web.php — Public Routes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    BookingController,
    GateTicketController,
    EventController,
    MenuController,
    GiftCardController,
    LoyaltyController,
    CareerController,
    MpesaCallbackController,
};

// ── Home & Static Pages ────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/rooms/{roomType:slug}', [HomeController::class, 'roomType'])->name('rooms.show');
Route::get('/food-drinks', [MenuController::class, 'index'])->name('menu');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// ── Room Booking ───────────────────────────────────────────
Route::prefix('book')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::post('/check', [BookingController::class, 'checkAvailability'])->name('check');
    Route::get('/select/{room}', [BookingController::class, 'select'])->name('select');
    Route::post('/reserve', [BookingController::class, 'reserve'])->name('reserve');
    Route::get('/payment/{booking}', [BookingController::class, 'payment'])->name('payment');
    Route::post('/pay/mpesa/{booking}', [BookingController::class, 'payMpesa'])->name('pay.mpesa');
    Route::get('/pay/mpesa/{booking}/status', [BookingController::class, 'pollPayment'])->name('pay.mpesa.poll');
    Route::get('/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');
});

// ── Gate Tickets ───────────────────────────────────────────
Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [GateTicketController::class, 'index'])->name('index');
    Route::post('/purchase', [GateTicketController::class, 'purchase'])->name('purchase');
    Route::get('/payment/{ticket}', [GateTicketController::class, 'payment'])->name('payment');
    Route::post('/pay/mpesa/{ticket}', [GateTicketController::class, 'payMpesa'])->name('pay.mpesa');
    Route::get('/confirmation/{ticket}', [GateTicketController::class, 'confirmation'])->name('confirmation');
});

// ── Events & Weddings ──────────────────────────────────────
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/packages', [EventController::class, 'packages'])->name('packages');
    Route::post('/inquire', [EventController::class, 'inquire'])->name('inquire');
});

// ── Gift Cards ─────────────────────────────────────────────
Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
    Route::get('/', [GiftCardController::class, 'index'])->name('index');
    Route::post('/purchase', [GiftCardController::class, 'purchase'])->name('purchase');
    Route::post('/redeem', [GiftCardController::class, 'redeem'])->name('redeem');
});

// ── Careers ────────────────────────────────────────────────
Route::prefix('careers')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/{job}', [CareerController::class, 'show'])->name('show');
    Route::post('/{job}/apply', [CareerController::class, 'apply'])->name('apply');
});

// ── Loyalty (requires auth) ────────────────────────────────
Route::middleware('auth')->prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [LoyaltyController::class, 'index'])->name('index');
    Route::get('/transactions', [LoyaltyController::class, 'transactions'])->name('transactions');
});

// ── M-Pesa Callback (CSRF exempt) ─────────────────────────
Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->post('/mpesa/callback', [MpesaCallbackController::class, 'handle'])
    ->name('mpesa.callback');

// ── Poll payment status (AJAX) ─────────────────────────────
Route::get('/book/pay/mpesa/{booking}/status', [\App\Http\Controllers\BookingController::class, 'pollPayment'])
    ->name('booking.pay.mpesa.poll');
Route::post('/tickets/pay/mpesa/{ticket}', [\App\Http\Controllers\GateTicketController::class, 'payMpesa'])
    ->name('tickets.pay.mpesa');
