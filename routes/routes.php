<?php

// ============================================================
// routes/web.php — Public Routes
// ============================================================

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
use App\Http\Controllers\Admin;

// --- Public ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/rooms/{roomType}', [HomeController::class, 'roomType'])->name('rooms.show');
Route::get('/food-drinks', [MenuController::class, 'index'])->name('menu');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// --- Booking (public) ---
Route::prefix('book')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::post('/check', [BookingController::class, 'checkAvailability'])->name('check');
    Route::get('/select/{room}', [BookingController::class, 'select'])->name('select');
    Route::post('/reserve', [BookingController::class, 'reserve'])->name('reserve');
    Route::get('/payment/{booking}', [BookingController::class, 'payment'])->name('payment');
    Route::post('/pay/mpesa/{booking}', [BookingController::class, 'payMpesa'])->name('pay.mpesa');
    Route::get('/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');
});

// --- Gate Tickets ---
Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [GateTicketController::class, 'index'])->name('index');
    Route::post('/purchase', [GateTicketController::class, 'purchase'])->name('purchase');
    Route::get('/payment/{ticket}', [GateTicketController::class, 'payment'])->name('payment');
    Route::get('/confirmation/{ticket}', [GateTicketController::class, 'confirmation'])->name('confirmation');
});

// --- Events ---
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/packages', [EventController::class, 'packages'])->name('packages');
    Route::post('/inquire', [EventController::class, 'inquire'])->name('inquire');
});

// --- Gift Cards ---
Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
    Route::get('/', [GiftCardController::class, 'index'])->name('index');
    Route::post('/purchase', [GiftCardController::class, 'purchase'])->name('purchase');
    Route::post('/redeem', [GiftCardController::class, 'redeem'])->name('redeem');
});

// --- Careers ---
Route::prefix('careers')->name('careers.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/{job}', [CareerController::class, 'show'])->name('show');
    Route::post('/{job}/apply', [CareerController::class, 'apply'])->name('apply');
});

// --- Loyalty (authenticated guests) ---
Route::middleware('auth')->prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [LoyaltyController::class, 'index'])->name('index');
    Route::get('/transactions', [LoyaltyController::class, 'transactions'])->name('transactions');
});

// --- M-Pesa Callback (no CSRF) ---
Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/mpesa/callback', [MpesaCallbackController::class, 'handle'])->name('mpesa.callback');
});

// ============================================================
// routes/admin.php — Admin Panel Routes
// ============================================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin|manager|receptionist|fnb_staff|housekeeper|hr_admin'])->group(function () {

    // Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [Admin\BookingController::class, 'index'])->name('index');
        Route::get('/create', [Admin\BookingController::class, 'create'])->name('create');
        Route::get('/check-availability', [Admin\BookingController::class, 'checkAvailability'])->name('check-availability');
        Route::post('/', [Admin\BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [Admin\BookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/check-in', [Admin\BookingController::class, 'checkIn'])->name('check-in');
        Route::patch('/{booking}/check-out', [Admin\BookingController::class, 'checkOut'])->name('check-out');
        Route::patch('/{booking}/cancel', [Admin\BookingController::class, 'cancel'])->name('cancel');
    });

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [Admin\RoomController::class, 'index'])->name('index');
        Route::get('/housekeeper', [Admin\RoomController::class, 'housekeeper'])->name('housekeeper');
        Route::patch('/{room}/status', [Admin\RoomController::class, 'updateStatus'])->name('status');
        Route::resource('types', Admin\RoomTypeController::class);
    });

    // Guests
    Route::resource('guests', Admin\GuestController::class);
    Route::get('guests/{guest}/loyalty', [Admin\GuestController::class, 'loyalty'])->name('guests.loyalty');

    // Restaurant
    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
        Route::resource('menu', Admin\MenuController::class);
        Route::resource('tables', Admin\TableController::class);
        Route::get('reservations', [Admin\TableController::class, 'reservations'])->name('reservations');
    });

    // Events
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [Admin\EventsController::class, 'index'])->name('index');
        Route::get('/{event}', [Admin\EventsController::class, 'show'])->name('show');
        Route::patch('/{event}/status', [Admin\EventsController::class, 'updateStatus'])->name('status');
        Route::resource('packages', Admin\EventPackageController::class);
    });

    // Gate Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [Admin\GateTicketController::class, 'index'])->name('index');
        Route::get('/scan', [Admin\GateTicketController::class, 'scan'])->name('scan');
        Route::post('/scan', [Admin\GateTicketController::class, 'processQr'])->name('scan.process');
    });

    // Gift Cards
    Route::resource('gift-cards', Admin\GiftCardController::class)->only(['index','show']);

    // HR / Careers
    Route::prefix('careers')->name('careers.')->group(function () {
        Route::resource('jobs', Admin\JobListingController::class);
        Route::get('applications', [Admin\JobApplicationController::class, 'index'])->name('applications');
        Route::get('applications/{application}', [Admin\JobApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/status', [Admin\JobApplicationController::class, 'updateStatus'])->name('applications.status');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/occupancy', [Admin\ReportsController::class, 'occupancy'])->name('occupancy');
        Route::get('/revenue', [Admin\ReportsController::class, 'revenue'])->name('revenue');
        Route::get('/guests', [Admin\ReportsController::class, 'guests'])->name('guests');
    });

    // Settings (super_admin only)
    Route::middleware('role:super_admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [Admin\SettingsController::class, 'index'])->name('index');
        Route::post('/', [Admin\SettingsController::class, 'update'])->name('update');
        Route::resource('users', Admin\UserController::class);
        Route::resource('pricing', Admin\PricingController::class);
    });
});
