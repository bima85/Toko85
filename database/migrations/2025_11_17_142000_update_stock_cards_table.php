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
            // Make stock_batch_id nullable first
            if (Schema::hasColumn('stock_cards', 'stock_batch_id')) {
                $table->foreignId('stock_batch_id')
                    ->nullable()
                    ->change();
            }

            // Add new columns after stock_batch_id
            if (!Schema::hasColumn('stock_cards', 'product_id')) {
                $table->foreignId('product_id')
                    ->nullable()
                    ->after('stock_batch_id')
                    ->constrained('products')
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('stock_cards', 'batch_id')) {
                $table->foreignId('batch_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('stock_batches')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('stock_cards', 'from_location')) {
                $table->string('from_location')
                    ->nullable()
                    ->after('qty')
                    ->comment('Lokasi asal (Supplier, Toko, Gudang, Customer)');
            }

            if (!Schema::hasColumn('stock_cards', 'to_location')) {
                $table->string('to_location')
                    ->nullable()
                    ->after('from_location')
                    ->comment('Lokasi tujuan (Toko, Gudang, Customer)');
            }

            if (!Schema::hasColumn('stock_cards', 'reference_type')) {
                $table->string('reference_type')
                    ->nullable()
                    ->after('reference')
                    ->comment('Tipe referensi: purchase, sale, adjustment');
            }

            if (!Schema::hasColumn('stock_cards', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')
                    ->nullable()
                    ->after('reference_type')
                    ->comment('ID referensi (purchase_id, sale_id, etc)');
            }

            // Rename 'notes' to 'note' if it exists
            if (Schema::hasColumn('stock_cards', 'notes') && !Schema::hasColumn('stock_cards', 'note')) {
                $table->renameColumn('notes', 'note');
            }

            // Add indexes
            $table->index('product_id');
            $table->index('batch_id');
            $table->index('reference_type');
            $table->index('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_cards', function (Blueprint $table) {
            // Drop new columns in reverse order
            if (Schema::hasColumn('stock_cards', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
            if (Schema::hasColumn('stock_cards', 'reference_type')) {
                $table->dropColumn('reference_type');
            }
            if (Schema::hasColumn('stock_cards', 'to_location')) {
                $table->dropColumn('to_location');
            }
            if (Schema::hasColumn('stock_cards', 'from_location')) {
                $table->dropColumn('from_location');
            }
            if (Schema::hasColumn('stock_cards', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('stock_cards', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }

            // Rename back 'note' to 'notes'
            if (Schema::hasColumn('stock_cards', 'note') && !Schema::hasColumn('stock_cards', 'notes')) {
                $table->renameColumn('note', 'notes');
            }
        });
    }
};
