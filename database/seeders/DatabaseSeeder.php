<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin account only if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin123@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'idno' => null,
                'course' => null,
                'year' => null,
                'section' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
