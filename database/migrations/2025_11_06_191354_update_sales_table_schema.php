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
        Schema::table('sales', function (Blueprint $table) {
            // Drop old columns if they exist and have data
            if (Schema::hasColumn('sales', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('sales', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('sales', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            // Add new columns
            if (!Schema::hasColumn('sales', 'no_invoice')) {
                $table->string('no_invoice', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('sales', 'tanggal_penjualan')) {
                $table->dateTime('tanggal_penjualan')->nullable()->after('no_invoice');
            }
            if (!Schema::hasColumn('sales', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade')->after('tanggal_penjualan');
            }
            if (!Schema::hasColumn('sales', 'store_id')) {
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null')->after('customer_id');
            }
            if (!Schema::hasColumn('sales', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->after('store_id');
            }
            if (!Schema::hasColumn('sales', 'status')) {
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed')->after('warehouse_id');
            }
            if (!Schema::hasColumn('sales', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop new columns if they exist
            if (Schema::hasColumn('sales', 'no_invoice')) {
                $table->dropColumn('no_invoice');
            }
            if (Schema::hasColumn('sales', 'tanggal_penjualan')) {
                $table->dropColumn('tanggal_penjualan');
            }
            if (Schema::hasColumn('sales', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('sales', 'store_id')) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            }
            if (Schema::hasColumn('sales', 'warehouse_id')) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }
            if (Schema::hasColumn('sales', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('sales', 'keterangan')) {
                $table->dropColumn('keterangan');
            }

            // Restore old columns
            $table->date('date')->after('id');
            $table->string('customer_name')->after('date');
            $table->decimal('total_amount', 15, 2)->after('customer_name');
        });
    }
};
