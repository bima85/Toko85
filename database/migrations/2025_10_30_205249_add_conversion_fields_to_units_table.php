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
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('parent_unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->decimal('conversion_value', 15, 6)->nullable();
            $table->boolean('is_base_unit')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['parent_unit_id']);
            $table->dropColumn(['parent_unit_id', 'conversion_value', 'is_base_unit']);
        });
    }
};
