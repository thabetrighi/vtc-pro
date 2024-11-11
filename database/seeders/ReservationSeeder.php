<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {  // Generate 50 random reservations
            Reservation::create([
                'mode' => $faker->randomElement(['transfer', 'trip']),
                'pickup_location' => $faker->address,
                'destination_location' => $faker->address,

                // Pickup address details
                'pickup_street' => $faker->streetAddress,
                'pickup_zip_code' => $faker->postcode,
                'pickup_city' => $faker->city,
                'pickup_note' => $faker->sentence,

                // Destination address details
                'destination_street' => $faker->streetAddress,
                'destination_zip_code' => $faker->postcode,
                'destination_city' => $faker->city,
                'destination_note' => $faker->sentence,

                // Date and time of departure
                'departure_at' => $faker->dateTimeBetween('-1 month', '+1 month'),

                // Passenger information
                'passenger_name' => $faker->name,
                'passenger_email' => $faker->email,
                'passenger_phone' => $faker->phoneNumber,
                'passenger_count' => $faker->numberBetween(1, 4),

                // Additional information
                'additional_info' => $faker->optional()->sentence,
                'alt_phone' => $faker->optional()->phoneNumber,

                // Payment and pricing
                'payment_method' => $faker->randomElement(['cash', 'card']),
                'fare' => $faker->randomFloat(2, 20, 200),  // Random fare between 20 and 200 with 2 decimal places
            ]);
        }
    }
}
