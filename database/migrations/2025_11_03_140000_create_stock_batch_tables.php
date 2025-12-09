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
      // Create stock_batches table
      Schema::create('stock_batches', function (Blueprint $table) {
         $table->id();
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->nullableMorphs('location'); // Polymorphic untuk Store atau Warehouse
         $table->string('nama_tumpukan')->default('Tumpukan');
         $table->decimal('qty', 10, 2)->default(0);
         $table->timestamps();

         // Indexes
         $table->index(['product_id', 'location_type']);
         $table->index('updated_at');
      });

      // Create stock_cards table (transaction log)
      Schema::create('stock_cards', function (Blueprint $table) {
         $table->id();
         $table->foreignId('stock_batch_id')->constrained('stock_batches')->onDelete('cascade');
         $table->enum('type', ['in', 'out', 'adjustment', 'move']);
         $table->decimal('qty', 10, 2);
         $table->text('notes')->nullable();
         $table->string('reference')->nullable(); // Purchase ID, Adjustment ID, etc.
         $table->timestamps();

         // Indexes
         $table->index(['stock_batch_id', 'type']);
         $table->index('created_at');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('stock_cards');
      Schema::dropIfExists('stock_batches');
   }
};
