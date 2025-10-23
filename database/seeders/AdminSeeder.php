<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin UrbanGreen',
            'email' => 'admin@urbangreen.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'admin yosr',
            'email' => 'yosr.mekki@esprit.tn',
            'password' => bcrypt('yos5112JFT7506+'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
