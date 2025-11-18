<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'sender@example.com',
            'password' => '12345678',
            'balance' => 10000000,
        ]);

        User::factory()->create([
            'email' => 'receiver@example.com',
            'password' => '12345678',
            'balance' => 0,
        ]);

    }
}
