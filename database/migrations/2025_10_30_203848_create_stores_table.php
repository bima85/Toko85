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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('kode_toko', 50)->unique();
            $table->string('nama_toko', 255);
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('pic', 255)->nullable(); // Person in charge
            $table->enum('tipe_toko', ['retail', 'wholesale', 'online', 'outlet'])->default('retail');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
