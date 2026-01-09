<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_histories', function (Blueprint $table) {
            // Drop unique constraint dari transaction_code
            $table->dropUnique(['transaction_code']);
            // Tambah index biasa (non-unique)
            $table->index('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_histories', function (Blueprint $table) {
            $table->dropIndex(['transaction_code']);
            $table->unique('transaction_code');
        });
    }
};
