<?php

namespace Database\Seeders;

use App\Models\Participation;
use App\Models\ParticipationFeedback;
use Illuminate\Database\Seeder;

class ParticipationFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $completedParticipations = Participation::with('feedback')
            ->where('statut', 'terminee')
            ->get();

        $completedParticipations->each(function (Participation $participation) {
            if ($participation->feedback) {
                return;
            }

            ParticipationFeedback::factory()
                ->forParticipation($participation)
                ->create([
                    'rating' => fake()->numberBetween(3, 5),
                    'comment' => fake()->sentences(3, true),
                ]);
        });
    }
}
