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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();                                                  // Primary key for the institutions table
            $table->string('name');                                        // Name of the institution
            $table->string('zip_code')->nullable();                        // Zip or postal code for the institution's address
            $table->string('city')->nullable();                            // City where the institution is located
            $table->string('number')->nullable();                          // Street number of the institution's address
            $table->string('street_name')->nullable();                     // Street name of the institution's address
            $table->timestamps();                                          // Timestamps for created_at and updated_at
            $table->softDeletes();                                         // Soft delete timestamp (deleted_at) for recoverable deletions
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
