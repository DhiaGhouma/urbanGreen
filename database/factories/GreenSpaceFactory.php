<?php

namespace Database\Factories;

use App\Models\GreenSpace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\GreenSpace>
 */
class GreenSpaceFactory extends Factory
{
    protected $model = GreenSpace::class;

    public function definition(): array
    {
    $types = ['parc', 'jardin', 'forêt', 'plage'];
    $statuses = ['proposé', 'en cours', 'terminé'];
    $complexityLevels = ['débutant', 'intermédiaire', 'expert'];

        return [
            'name' => fake()->unique()->streetName . ' Green Space',
            'location' => fake()->city(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement($types),
            'complexity_level' => fake()->randomElement($complexityLevels),
            'surface' => fake()->numberBetween(500, 50000),
            'latitude' => fake()->latitude(33, 37),
            'longitude' => fake()->longitude(8, 11),
            'status' => fake()->randomElement($statuses),
            'photos_before' => [],
            'photos_after' => [],
            'activities' => fake()->randomElements([
                'reboisement', 'nettoyage', 'atelier compost', 'randonnée', 'yoga', 'jardinage',
            ], 3),
        ];
    }
}
