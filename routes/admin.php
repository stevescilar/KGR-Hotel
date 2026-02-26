<?php

// routes/admin.php — Admin Panel Routes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    BookingController,
    RoomController,
    RoomTypeController,
    GuestController,
    OrderController,
    MenuController,
    TableController,
    EventsController,
    EventPackageController,
    GateTicketController,
    GiftCardController,
    JobListingController,
    JobApplicationController,
    ReportsController,
    SettingsController,
    UserController,
    PricingController,
};

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:super_admin|manager|receptionist|fnb_staff|housekeeper|hr_admin'])
    ->group(function () {

        // ── Dashboard ──────────────────────────────────────
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ── Bookings ───────────────────────────────────────
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/create', [BookingController::class, 'create'])->name('create');
            Route::get('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');
            Route::post('/', [BookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
            Route::patch('/{booking}/check-in', [BookingController::class, 'checkIn'])->name('check-in');
            Route::patch('/{booking}/check-out', [BookingController::class, 'checkOut'])->name('check-out');
            Route::patch('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        });

        // ── Rooms ──────────────────────────────────────────
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [RoomController::class, 'index'])->name('index');
            Route::get('/housekeeper', [RoomController::class, 'housekeeper'])->name('housekeeper');
            Route::patch('/{room}/status', [RoomController::class, 'updateStatus'])->name('status');
            Route::resource('types', RoomTypeController::class);
        });

        // ── Guests ─────────────────────────────────────────
        Route::get('guests/{guest}/loyalty', [GuestController::class, 'loyalty'])->name('guests.loyalty');
        Route::resource('guests', GuestController::class);

        // ── Restaurant ─────────────────────────────────────
        Route::prefix('restaurant')->name('restaurant.')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
            Route::resource('menu', MenuController::class);
            Route::get('reservations', [TableController::class, 'reservations'])->name('reservations');
            Route::resource('tables', TableController::class);
        });

        // ── Events ─────────────────────────────────────────
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [EventsController::class, 'index'])->name('index');
            Route::get('/{event}', [EventsController::class, 'show'])->name('show');
            Route::patch('/{event}/status', [EventsController::class, 'updateStatus'])->name('status');
            Route::resource('packages', EventPackageController::class);
        });

        // ── Gate Tickets ───────────────────────────────────
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [GateTicketController::class, 'index'])->name('index');
            Route::get('/scan', [GateTicketController::class, 'scan'])->name('scan');
            Route::post('/scan', [GateTicketController::class, 'processQr'])->name('scan.process');
        });

        // ── Gift Cards ─────────────────────────────────────
        Route::resource('gift-cards', GiftCardController::class)->only(['index', 'show']);

        // ── HR / Careers ───────────────────────────────────
        Route::prefix('careers')->name('careers.')->group(function () {
            Route::resource('jobs', JobListingController::class);
            Route::get('applications', [JobApplicationController::class, 'index'])->name('applications');
            Route::get('applications/{application}', [JobApplicationController::class, 'show'])->name('applications.show');
            Route::patch('applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('applications.status');
        });

        // ── Reports ────────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/occupancy', [ReportsController::class, 'occupancy'])->name('occupancy');
            Route::get('/revenue', [ReportsController::class, 'revenue'])->name('revenue');
            Route::get('/guests', [ReportsController::class, 'guests'])->name('guests');
        });

        // ── Settings (super_admin only) ────────────────────
        Route::middleware('role:super_admin')
            ->prefix('settings')
            ->name('settings.')
            ->group(function () {
                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::post('/', [SettingsController::class, 'update'])->name('update');
                Route::resource('users', UserController::class);
                Route::resource('pricing', PricingController::class);
            });
    });
