<?php

// ============================================================
// KITONGA GARDEN RESORT — DATABASE MIGRATIONS
// Run: php artisan migrate
// ============================================================

// -------------------------------------------------------
// FILE: database/migrations/2024_01_01_000001_create_room_types_table.php
// -------------------------------------------------------
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // --- ROOM TYPES ---
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. Standard, Deluxe, Penthouse
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->decimal('base_price', 10, 2);           // Per night KES
            $table->decimal('weekend_price', 10, 2)->nullable();
            $table->json('amenities')->nullable();           // ["WiFi","Balcony","AC"]
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // --- ROOMS ---
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('room_number')->unique();
            $table->string('floor')->nullable();
            $table->string('cottage')->nullable();           // Cottage A, B, C…
            $table->enum('status', ['available','occupied','maintenance','cleaning'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // --- GUESTS ---
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->enum('vip_tier', ['none','bronze','silver','gold'])->default('none');
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // --- BOOKINGS ---
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_ref')->unique();        // KGR-2024-00001
            $table->foreignId('room_id')->constrained();
            $table->foreignId('guest_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // staff who created
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('room_rate', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending','confirmed','checked_in','checked_out','cancelled','no_show'])->default('pending');
            $table->enum('payment_status', ['unpaid','partial','paid','refunded'])->default('unpaid');
            $table->string('source')->default('website');   // website, walk_in, phone, ota
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // --- PAYMENTS ---
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->morphs('payable');                       // booking, ticket, event, gift_card
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('KES');
            $table->enum('method', ['mpesa','card','cash','bank_transfer','gift_card','loyalty_points']);
            $table->enum('status', ['pending','completed','failed','refunded'])->default('pending');
            $table->string('provider_reference')->nullable(); // Mpesa TransactionID
            $table->json('provider_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // --- SEASONAL PRICING ---
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 10, 2);
            $table->integer('min_nights')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // --- BLOCKED DATES ---
        Schema::create('room_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('blocked_from');
            $table->date('blocked_to');
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // -------------------------------------------------------
        // FOOD & DRINKS
        // -------------------------------------------------------

        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->enum('type', ['food','drinks','desserts'])->default('food');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->json('tags')->nullable();               // ["vegetarian","spicy"]
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->integer('capacity');
            $table->string('section')->nullable();          // Indoor, Outdoor, Garden
            $table->enum('status', ['available','reserved','occupied'])->default('available');
            $table->timestamps();
        });

        Schema::create('table_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('table_id')->constrained();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->integer('party_size');
            $table->dateTime('reserved_at');
            $table->integer('duration_minutes')->default(90);
            $table->enum('status', ['pending','confirmed','seated','completed','cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['dine_in','room_service','takeaway'])->default('dine_in');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['open','preparing','served','billed','paid','cancelled'])->default('open');
            $table->enum('payment_status', ['unpaid','paid','charged_to_room'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // -------------------------------------------------------
        // EVENTS & WEDDINGS
        // -------------------------------------------------------

        Schema::create('event_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('starting_price', 10, 2);
            $table->integer('min_guests')->default(10);
            $table->integer('max_guests')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('event_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type');                   // Wedding, Birthday, Conference…
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('guest_count');
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->decimal('deposit_paid', 10, 2)->default(0);
            $table->enum('status', ['inquiry','quoted','confirmed','completed','cancelled'])->default('inquiry');
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // -------------------------------------------------------
        // GATE TICKETING
        // -------------------------------------------------------

        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // Adult, Child, Student, Group
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('gate_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('qr_code')->unique();
            $table->foreignId('ticket_type_id')->constrained();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->string('guest_email')->nullable();
            $table->date('visit_date');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 8, 2);
            $table->enum('status', ['active','used','expired','cancelled'])->default('active');
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // -------------------------------------------------------
        // LOYALTY PROGRAM
        // -------------------------------------------------------

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->integer('points');                      // Positive = earn, negative = redeem
            $table->string('type');                         // booking, checkin, restaurant, redemption, bonus
            $table->string('description');
            $table->morphs('referenceable');               // Polymorphic: booking, order, etc.
            $table->integer('balance_after');
            $table->timestamps();
        });

        // -------------------------------------------------------
        // GIFT CARDS
        // -------------------------------------------------------

        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('original_value', 8, 2);
            $table->decimal('remaining_value', 8, 2);
            $table->string('purchased_by_name')->nullable();
            $table->string('purchased_by_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('message')->nullable();
            $table->date('expires_at')->nullable();
            $table->enum('status', ['active','partially_used','fully_used','expired','cancelled'])->default('active');
            $table->timestamps();
        });

        // -------------------------------------------------------
        // CAREERS / HR
        // -------------------------------------------------------

        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->string('location')->default('Kitonga Garden Resort');
            $table->enum('type', ['full_time','part_time','contract','internship']);
            $table->text('description');
            $table->text('requirements');
            $table->decimal('salary_min', 8, 2)->nullable();
            $table->decimal('salary_max', 8, 2)->nullable();
            $table->date('closing_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('cv_path');
            $table->string('cover_letter_path')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['received','reviewing','shortlisted','interviewed','offered','rejected','hired'])->default('received');
            $table->text('hr_notes')->nullable();
            $table->timestamps();
        });

        // -------------------------------------------------------
        // SYSTEM
        // -------------------------------------------------------

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'activity_logs','settings','job_applications','job_listings',
            'gift_cards','loyalty_transactions','gate_tickets','ticket_types',
            'event_bookings','event_packages','order_items','orders',
            'table_reservations','tables','menu_items','menu_categories',
            'room_blocks','pricing_rules','payments','bookings','guests',
            'rooms','room_types',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
