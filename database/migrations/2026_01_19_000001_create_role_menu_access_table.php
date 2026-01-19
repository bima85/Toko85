<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_menu_access', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->string('route_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_menu_access');
    }
};
