<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_cards MODIFY COLUMN `type` ENUM('in', 'out', 'adjustment', 'move', 'hold', 'cancel_hold', 'sale', 'return') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stock_cards MODIFY COLUMN `type` ENUM('in', 'out', 'adjustment', 'move', 'hold', 'cancel_hold', 'sale') NOT NULL");
    }
};
