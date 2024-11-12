<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Faker\Factory as Faker;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) { // Generate 50 random reservations
            $departureAt = $faker->dateTimeBetween('-1 month', '+1 month');
            $currentDate = now();

            // Determine status based on departure date
            $status = $this->determineStatus($departureAt, $currentDate);

            Reservation::create([
                'mode' => $faker->randomElement(['transfer', 'ride']),
                'pickup_location' => $faker->address,
                'destination_location' => $faker->address,

                // Pickup address details
                'pickup_street' => $faker->streetAddress,
                'pickup_zip_code' => $faker->postcode,
                'pickup_city' => $faker->city,
                'pickup_note' => $faker->optional()->sentence,

                // Destination address details
                'destination_street' => $faker->streetAddress,
                'destination_zip_code' => $faker->postcode,
                'destination_city' => $faker->city,
                'destination_note' => $faker->optional()->sentence,

                // Date and time of departure
                'departure_at' => $departureAt,

                // Passenger information
                'passenger_name' => $faker->name,
                'passenger_email' => $faker->safeEmail,
                'passenger_phone' => $faker->phoneNumber,
                'passenger_count' => $faker->numberBetween(1, 4),

                // Additional information
                'additional_info' => $faker->optional()->sentence,

                // Payment and pricing
                'payment_method' => $faker->randomElement(['cash', 'card']),
                'fare' => $faker->randomFloat(2, 20, 200), // Random fare between 20 and 200 with 2 decimal places
                'user_id' => 1, // admin

                // Status
                'status' => $status,
            ]);
        }
    }

    /**
     * Determine reservation status based on departure date.
     *
     * @param \DateTime $departureAt
     * @param \DateTime $currentDate
     * @return string
     */
    private function determineStatus($departureAt, $currentDate): string
    {
        if ($departureAt > $currentDate) {
            return 'pending';
        }

        $statusOptions = ['ongoing', 'completed', 'canceled'];

        return $departureAt < $currentDate->modify('-1 day')
            ? $statusOptions[array_rand(['completed', 'canceled'])] // Past dates randomly set to completed or canceled
            : 'ongoing';
    }
}
