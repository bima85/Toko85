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
        Schema::table('stock_batches', function (Blueprint $table) {
            // Tambah column untuk track status batch (aktual atau hold)
            $table->enum('status', ['aktual', 'hold'])->default('aktual')->after('note');

            // Index untuk query cepat
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropIndex(['status']);
        });
    }
};
