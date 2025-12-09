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
        Schema::table('purchases', function (Blueprint $table) {
            $table->renameColumn('kode_pembelian', 'no_invoice');
            $table->dropColumn('total_pembelian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->renameColumn('no_invoice', 'kode_pembelian');
            $table->decimal('total_pembelian', 15, 2)->default(0);
        });
    }
};
