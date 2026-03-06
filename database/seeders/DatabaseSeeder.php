<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account
        User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);

        // Regular user account
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);

        $this->call([
            ActivitySeeder::class ,
        ]);
    }
}
