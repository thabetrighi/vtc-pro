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
            $table->id();                                      // Primary key for the cars table
            $table->string('color')->nullable();               // Color of the car (e.g., white, black)
            $table->string('model')->nullable();               // Model of the car (e.g., Ioniq, Accord)
            $table->string('brand')->nullable();               // Brand or make of the car (e.g., Hyundai, Toyota)
            $table->string('category')->nullable();            // Category of the car (e.g., Limousine, Sedan, SUV)
            $table->integer('seat_number')->nullable();        // Number of seats in the car
            $table->string('energy_type')->nullable();         // Type of energy (e.g., Electric, Gasoline, Diesel)
            $table->string('registration_plate')->unique();    // Unique registration plate number of the car

            $table->foreignId('user_id')                       // Foreign key linking to the user's ID
                ->nullable()                                   // Nullable to allow cars not yet assigned to a user
                ->constrained('users')                         // Constraint referencing the users table
                ->onDelete('set null');                        // Set to null if the related user is deleted

            $table->timestamps();                              // Timestamps for created_at and updated_at
            $table->softDeletes();                             // Soft delete timestamp (deleted_at) for recoverable deletions
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
