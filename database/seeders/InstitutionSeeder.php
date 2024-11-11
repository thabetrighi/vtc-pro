<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;
use Faker\Factory as Faker;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {  // Generate 50 random institutions
            Institution::create([
                'name' => $faker->company,                     // Random institution/company name
                'zip_code' => $faker->postcode,                // Random zip code
                'city' => $faker->city,                        // Random city name
                'number' => $faker->buildingNumber,            // Random street number
                'street_name' => $faker->streetName,           // Random street name
                'created_at' => now(),                         // Current timestamp for creation
                'updated_at' => now(),                         // Current timestamp for update
            ]);
        }
    }
}
