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
        Schema::create('reallogistic_reserve', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('box_id', 10);
            $table->integer('start');
            $table->integer('finish');
            $table->date('date_start')->nullable();
            $table->date('date_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reallogistic_reserve');
    }
};
