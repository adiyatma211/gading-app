<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            // Time windows (24h HH:MM:SS)
            $table->time('check_in_on_time_until')->default('09:00:00'); // e.g., on-time if <= 09:00
            $table->time('check_in_last_allowed')->default('12:00:00'); // last allowed to check-in
            $table->time('check_out_earliest')->default('17:00:00');     // earliest allowed to check-out
            $table->time('check_out_latest')->default('23:59:59');       // last allowed to check-out
            $table->boolean('enable_weekends')->default(true);           // Senin-Minggu allowed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};

