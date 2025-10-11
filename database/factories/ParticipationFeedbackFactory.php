<?php

namespace Database\Factories;

use App\Models\Participation;
use App\Models\ParticipationFeedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ParticipationFeedback>
 */
class ParticipationFeedbackFactory extends Factory
{
    protected $model = ParticipationFeedback::class;

    public function definition(): array
    {
        return [
            'participation_id' => Participation::factory()->state(fn () => [
                'statut' => 'terminee',
            ]),
            'user_id' => null,
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->paragraph(3, true),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (ParticipationFeedback $feedback) {
            if (!$feedback->user_id && $feedback->participation) {
                $feedback->user_id = $feedback->participation->user_id;
            }
        })->afterCreating(function (ParticipationFeedback $feedback) {
            if (!$feedback->user_id && $feedback->participation) {
                $feedback->user_id = $feedback->participation->user_id;
                $feedback->save();
            }
        });
    }

    public function forParticipation(Participation $participation): self
    {
        return $this->state(fn () => [
            'participation_id' => $participation->id,
            'user_id' => $participation->user_id,
        ]);
    }
}
