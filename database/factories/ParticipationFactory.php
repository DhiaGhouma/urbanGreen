<?php

namespace Database\Factories;

use App\Models\Participation;
use App\Models\User;
use App\Models\GreenSpace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Participation>
 */
class ParticipationFactory extends Factory
{
    protected $model = Participation::class;

    public function definition(): array
    {
        $days = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
        $availability = ['matin','après-midi','soir'];
        $interests = ['reboisement','nettoyage','jardinage','compostage','photographie','botanique','randonnée'];

        return [
            'user_id' => User::factory(),
            'green_space_id' => GreenSpace::factory(),
            'date' => now()->addDays(fake()->numberBetween(0, 30)),
            'statut' => fake()->randomElement(['en_attente','confirmee','annulee','terminee']),
            'preferences' => [
                'prefered_days' => fake()->randomElements($days, fake()->numberBetween(1,3)),
                'availability' => [fake()->randomElement($availability)],
                'activities_interest' => fake()->randomElements($interests, fake()->numberBetween(1,3)),
                'role' => fake()->randomElement(['bénévole','coordinateur','formateur']),
                'notes' => fake()->sentence(8)
            ],
        ];
    }
}
