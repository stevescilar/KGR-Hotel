<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add image to rooms ────────────────────────────────
        if (!Schema::hasColumn('rooms', 'image')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('image')->nullable()->after('notes');
            });
        }

        // ── Add images to room_types ──────────────────────────
        if (!Schema::hasColumn('room_types', 'images')) {
            Schema::table('room_types', function (Blueprint $table) {
                $table->json('images')->nullable()->after('amenities');
            });
        }

        // ── Add image to event_packages ───────────────────────
        if (!Schema::hasColumn('event_packages', 'image')) {
            Schema::table('event_packages', function (Blueprint $table) {
                $table->string('image')->nullable()->after('is_active');
            });
        }

        // ── Add image to menu_items ───────────────────────────
        if (!Schema::hasColumn('menu_items', 'image')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->string('image')->nullable()->after('sort_order');
            });
        }

        // ── Add image to menu_categories ──────────────────────
        if (!Schema::hasColumn('menu_categories', 'image')) {
            Schema::table('menu_categories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('name');
            });
        }

        // ── Walk-in tickets table ─────────────────────────────
        if (!Schema::hasTable('walk_in_tickets')) {
            Schema::create('walk_in_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
                $table->string('guest_name');
                $table->string('guest_phone')->nullable();
                $table->string('guest_id_number')->nullable();
                $table->date('valid_date');
                $table->decimal('amount', 10, 2)->default(1500);
                $table->string('status')->default('active'); // active, used, expired
                $table->string('qr_code')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_tickets');
    }
};