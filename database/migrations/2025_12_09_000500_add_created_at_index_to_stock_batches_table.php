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
        if (!Schema::hasTable('stock_batches')) {
            return;
        }

        Schema::table('stock_batches', function (Blueprint $table) {
            // Add created_at index for faster grouping by latest batch
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('stock_batches')) {
            return;
        }

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
