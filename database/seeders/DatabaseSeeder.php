<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@jokincay.test'],
            [
                'name' => 'Admin Jokincay',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
    }
}
