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
        Schema::create('reallogistic_boxes_price', function (Blueprint $table) {
            $table->id(); // Clé primaire Laravel auto-incrémentée
            $table->timestamps();
            $table->string('box_id', 10)->unique(); // box_id doit être unique mais n'est pas une clé primaire
            $table->decimal('price')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reallogistic_boxes_price');
        
    }
};
