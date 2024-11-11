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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['transfer', 'trip'])->default('trip'); // Mode: Transfer or Trip
            $table->string('pickup_location');               // Pickup location
            $table->string('destination_location');          // Destination location

            // Pickup address details
            $table->string('pickup_street')->nullable();     // Street name and number for pickup
            $table->string('pickup_zip_code')->nullable();   // Zip code for pickup
            $table->string('pickup_city')->nullable();       // City for pickup
            $table->string('pickup_note')->nullable();       // Note at pickup

            // Destination address details
            $table->string('destination_street')->nullable();   // Street name and number for destination
            $table->string('destination_zip_code')->nullable(); // Zip code for destination
            $table->string('destination_city')->nullable();     // City for destination
            $table->string('destination_note')->nullable();     // Note at destination

            // Date and time of departure
            $table->timestamp('departure_at');                // Date and time of departure

            // Passenger information
            $table->string('passenger_name');                 // Full name of passenger
            $table->string('passenger_email')->nullable();    // Passenger email
            $table->string('passenger_phone')->nullable();    // Passenger phone number
            $table->integer('passenger_count')->default(1);   // Number of passengers

            // Additional information
            $table->text('additional_info')->nullable();      // Additional information or special requests
            $table->string('alt_phone')->nullable();          // Alternate phone number

            // Payment and pricing
            $table->enum('payment_method', ['cash', 'card'])->default('cash'); // Payment method (Cash or Card)
            $table->decimal('fare', 8, 2);                    // Fare in total including taxes

            $table->foreignId('user_id')                       // Foreign key linking to the user's ID
                ->nullable()                                   // Nullable to allow cars not yet assigned to a user
                ->constrained('users')                         // Constraint referencing the users table
                ->onDelete('set null');                        // Set to null if the related user is deleted

            // Timestamps for created and updated times
            $table->timestamps();
            $table->softDeletes();                            // Soft delete timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
