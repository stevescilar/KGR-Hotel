<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add meal plan & deposit to bookings ───────────────
        if (!Schema::hasColumn('bookings', 'meal_plan')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('meal_plan')->default('room_only')->after('special_requests');
            });
        }

        if (!Schema::hasColumn('bookings', 'deposit_amount')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->decimal('deposit_amount', 10, 2)->default(0)->after('meal_plan');
            });
        }

        if (!Schema::hasColumn('bookings', 'payment_option')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('payment_option')->default('full')->after('deposit_amount');
            });
        }

        // ── Give room_rate a default so it never blocks inserts ──
        // (runs safely even if column already exists with a default)
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE bookings MODIFY room_rate decimal(10,2) NOT NULL DEFAULT 0');
        } catch (\Exception $e) {
            // Column may already have a default or not exist — safe to ignore
        }

        // ── Add account creation token to guests ──────────────
        if (!Schema::hasColumn('guests', 'account_token')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->string('account_token', 64)->nullable()->unique()->after('email');
            });
        }
        if (!Schema::hasColumn('guests', 'account_token_expires_at')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->timestamp('account_token_expires_at')->nullable()->after('account_token');
            });
        }
        if (!Schema::hasColumn('guests', 'user_id')) {
            Schema::table('guests', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('account_token_expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['meal_plan', 'deposit_amount', 'payment_option']);
        });
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['account_token', 'account_token_expires_at', 'user_id']);
        });
    }
};