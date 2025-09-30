<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrateur UrbanGreen',
            'email' => 'admin@urbangreen.fr',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
        ]);

        // Test users with specific profiles
        $testUsers = [
            [
                'name' => 'Marie Jardinage',
                'email' => 'marie@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'moderator',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Pierre Écologie',
                'email' => 'pierre@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Sophie Nature',
                'email' => 'sophie@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Lucas Biodiversité',
                'email' => 'lucas@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Emma Citoyenne',
                'email' => 'emma@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Thomas Environnement',
                'email' => 'thomas@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Camille Verte',
                'email' => 'camille@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ],
            [
                'name' => 'Antoine Durable',
                'email' => 'antoine@example.fr',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'failed_login_attempts' => 0,
            ]
        ];

        foreach ($testUsers as $userData) {
            User::create($userData);
        }

        // Generate random users using factory
        User::factory()->count(20)->create();

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin login: admin@urbangreen.fr / admin123');
        $this->command->info('Test users password: password');
    }
}
