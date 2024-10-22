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
        Schema::create('reallogistic_reserve_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('reserve_ids');
            $table->string('category', 100)->nullable();
            $table->string('file', 50)->nullable();
            $table->text('comment')->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reallogistic_reserve_data');
    }
};
