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
        if (! Schema::hasColumn('stock_batches', 'category_id')) {
            Schema::table('stock_batches', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('product_id');
            });

            Schema::table('stock_batches', function (Blueprint $table) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('stock_batches', 'subcategory_id')) {
            Schema::table('stock_batches', function (Blueprint $table) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
            });

            Schema::table('stock_batches', function (Blueprint $table) {
                $table->foreign('subcategory_id')
                    ->references('id')
                    ->on('subcategories')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            if (Schema::hasColumn('stock_batches', 'subcategory_id')) {
                $table->dropForeign(['subcategory_id']);
                $table->dropColumn('subcategory_id');
            }

            if (Schema::hasColumn('stock_batches', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};
