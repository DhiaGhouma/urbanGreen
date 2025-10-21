<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipationFeedbackRequest;
use App\Http\Requests\UpdateParticipationFeedbackRequest;
use App\Models\Participation;
use Illuminate\Http\RedirectResponse;

class ParticipationFeedbackController extends Controller
{
    public function store(StoreParticipationFeedbackRequest $request, Participation $participation): RedirectResponse
    {
        $this->ensureUserCanManage($participation);
        $this->ensureParticipationIsCompleted($participation);

        if ($participation->feedback) {
            return redirect()
                ->route('participations.show', $participation)
                ->with('info', 'Un avis existe déjà pour cette participation.');
        }

        $participation->feedback()->create([
            'user_id' => $participation->user_id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()
            ->route('participations.show', $participation)
            ->with('success', 'Merci pour votre retour !');
    }

    public function update(UpdateParticipationFeedbackRequest $request, Participation $participation): RedirectResponse
    {
        $this->ensureUserCanManage($participation);
        $this->ensureParticipationIsCompleted($participation);

        $feedback = $participation->feedback;

        if (!$feedback) {
            return redirect()
                ->route('participations.show', $participation)
                ->with('error', 'Aucun avis à mettre à jour.');
        }

        $feedback->update($request->validated());

        return redirect()
            ->route('participations.show', $participation)
            ->with('success', 'Votre avis a été mis à jour.');
    }

    public function destroy(Participation $participation): RedirectResponse
    {
        $this->ensureUserCanManage($participation);
        $this->ensureParticipationIsCompleted($participation);

        $feedback = $participation->feedback;

        if (!$feedback) {
            return redirect()
                ->route('participations.show', $participation)
                ->with('info', 'Aucun avis à supprimer.');
        }

        $feedback->delete();

        return redirect()
            ->route('participations.show', $participation)
            ->with('success', 'Votre avis a été supprimé.');
    }

    protected function ensureUserCanManage(Participation $participation): void
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && $user->id !== $participation->user_id)) {
            abort(403, 'Vous ne pouvez gérer que vos propres avis.');
        }
    }

    protected function ensureParticipationIsCompleted(Participation $participation): void
    {
        if ($participation->statut !== 'terminee') {
            abort(403, 'Vous ne pouvez laisser un avis que pour les participations terminées.');
        }
    }
}
