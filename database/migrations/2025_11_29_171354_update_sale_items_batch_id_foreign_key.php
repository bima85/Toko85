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
        Schema::table('sale_items', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign('sale_items_batch_id_foreign');

            // Recreate with CASCADE
            $table->foreign('batch_id')->references('id')->on('stock_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Drop the cascade foreign key
            $table->dropForeign('sale_items_batch_id_foreign');

            // Recreate with RESTRICT
            $table->foreign('batch_id')->references('id')->on('stock_batches')->onDelete('restrict');
        });
    }
};
