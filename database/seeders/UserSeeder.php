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
        // Admin user (idempotent)
        User::updateOrCreate(
            ['email' => 'admin@urbangreen.fr'],
            [
                'name' => 'Administrateur UrbanGreen',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'failed_login_attempts' => 0,
                'last_login_at' => now(),
            ]
        );

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
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Ensure at least 20 random users exist without duplicating
        $currentRandomCount = User::whereNotIn('email', array_column($testUsers, 'email'))
            ->where('email', '!=', 'admin@urbangreen.fr')
            ->count();
        if ($currentRandomCount < 20) {
            User::factory()->count(20 - $currentRandomCount)->create();
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin login: admin@urbangreen.fr / admin123');
        $this->command->info('Test users password: password');
    }
}
