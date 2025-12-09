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
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique()->comment('Kode transaksi unik');
            $table->string('transaction_type')->comment('Tipe transaksi: penjualan, pembelian, adjustment, dll');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID referensi (sale_id, purchase_id, etc)');
            $table->string('reference_type')->nullable()->comment('Tipe referensi: Sale, Purchase, StockAdjustment, dll');
            $table->dateTime('transaction_date')->comment('Tanggal transaksi');
            $table->decimal('amount', 15, 2)->comment('Jumlah transaksi');
            $table->string('currency', 3)->default('IDR')->comment('Mata uang');
            $table->string('description')->nullable()->comment('Deskripsi transaksi');
            $table->string('status')->default('completed')->comment('Status: pending, completed, failed, cancelled');
            $table->unsignedBigInteger('user_id')->comment('User yang melakukan transaksi');
            $table->string('payment_method')->nullable()->comment('Metode pembayaran');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->json('metadata')->nullable()->comment('Data tambahan dalam format JSON');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('user_id');
            $table->index('status');
            $table->index('reference_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};
