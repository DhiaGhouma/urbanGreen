<?php

namespace App\Http\Requests;

use App\Models\Participation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipationFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = $this->user();
        /** @var Participation $participation */
        $participation = $this->route('participation');

        if (!$user || !$participation) {
            return false;
        }

        return $user->isAdmin() || $participation->user_id === $user->id;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10'],
        ];
    }
}
