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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Toyota Prius"
            $table->string('type'); // e.g., "Sedan", "SUV", "Hatchback"
            $table->decimal('price_per_day', 10, 2); // Price in Rs/per day
            $table->string('transmission'); // e.g., "Manual", "Automatic"
            $table->integer('seating_capacity'); // e.g., 5
            $table->string('fuel_type'); // e.g., "Petrol", "Diesel", "Hybrid"
            $table->string('image_url')->nullable();
            $table->text('features')->nullable(); // JSON or comma-separated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
