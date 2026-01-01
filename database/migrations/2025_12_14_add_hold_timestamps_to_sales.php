<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tambah timestamps untuk tracking hold/cancel/complete
            $table->timestamp('held_at')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('held_at');
            $table->timestamp('completed_at')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['held_at', 'cancelled_at', 'completed_at']);
        });
    }
};
