<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Dashboard;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = 'admin@monacib1.net';

        // Check if an admin user already exists with this email
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            $admin = Dashboard::modelClass(User::class)::create([
                'name' => 'admin',
                'email' => $adminEmail,
                'password' => Hash::make('admin'),
                'permissions' => Dashboard::getAllowAllPermission(),
            ]);

            $this->command->info('Default admin user created successfully.');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
