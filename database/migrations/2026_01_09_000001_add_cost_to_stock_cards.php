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
        Schema::table('stock_cards', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_cards', 'cost')) {
                $table->decimal('cost', 12, 2)->nullable()->after('qty')->comment('Snapshot harga/biaya per unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_cards', function (Blueprint $table) {
            if (Schema::hasColumn('stock_cards', 'cost')) {
                $table->dropColumn('cost');
            }
        });
    }
};
