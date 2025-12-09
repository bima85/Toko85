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
      Schema::table('stock_adjustments', function (Blueprint $table) {
         $table->foreignId('unit_id')->nullable()->after('quantity')->constrained('units')->nullOnDelete();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('stock_adjustments', function (Blueprint $table) {
         $table->dropForeignIdFor('Unit');
         $table->dropColumn('unit_id');
      });
   }
};
