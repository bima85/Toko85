<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'hold', 'cancel_hold', and 'sale' to stock_cards type enum
        DB::statement("ALTER TABLE stock_cards MODIFY COLUMN type ENUM('in', 'out', 'adjustment', 'move', 'hold', 'cancel_hold', 'sale') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove hold types from enum
        DB::statement("ALTER TABLE stock_cards MODIFY COLUMN type ENUM('in', 'out', 'adjustment', 'move') NOT NULL");
    }
};
