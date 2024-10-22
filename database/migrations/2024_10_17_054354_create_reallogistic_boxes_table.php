<?php

use Illuminate\Container\Attributes\DB;
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
        Schema::create('reallogistic_boxes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title', 2)->unique();
            $table->smallInteger('amount')->unsigned();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reallogistic_boxes');
    }
};
