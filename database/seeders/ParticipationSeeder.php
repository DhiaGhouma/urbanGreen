<?php

namespace Database\Seeders;

use App\Models\GreenSpace;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ParticipationSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::where('role', '!=', 'admin')->pluck('id')->all();
        $greenSpaceIds = GreenSpace::pluck('id')->all();

        if (empty($userIds) || empty($greenSpaceIds)) {
            return;
        }

        // Generate a mix of participations across users and green spaces
        Participation::factory()
            ->count(25)
            ->state(function () use ($userIds, $greenSpaceIds) {
                return [
                    'user_id' => Arr::random($userIds),
                    'green_space_id' => Arr::random($greenSpaceIds),
                    'date' => now()->subDays(fake()->numberBetween(0, 60)),
                    'statut' => Arr::random(['en_attente', 'confirmee', 'annulee', 'terminee']),
                ];
            })
            ->create();
    }
}
