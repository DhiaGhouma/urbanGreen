<?php

namespace App\Observers;

use App\Models\Participation;
use App\Mail\ParticipationCompleted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ParticipationObserver
{
    /**
     * Handle the Participation "updated" event.
     * 
     * This observer monitors when a participation status changes to "terminee"
     * and automatically sends a thank you email to the participant.
     */
    public function updated(Participation $participation): void
    {
        // Check if the status was changed
        if ($participation->isDirty('statut')) {
            $oldStatus = $participation->getOriginal('statut');
            $newStatus = $participation->statut;

            // If status changed to "terminee", send thank you email
            if ($newStatus === 'terminee' && $oldStatus !== 'terminee') {
                try {
                    // Load relationships if not already loaded
                    $participation->loadMissing(['user', 'greenSpace']);

                    // Send email
                    Mail::to($participation->user->email)
                        ->send(new ParticipationCompleted($participation));

                    Log::info('Participation completed email sent', [
                        'participation_id' => $participation->id,
                        'user_id' => $participation->user_id,
                        'user_email' => $participation->user->email,
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't fail the update
                    Log::error('Failed to send participation completed email', [
                        'participation_id' => $participation->id,
                        'user_id' => $participation->user_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Participation "created" event.
     */
    public function created(Participation $participation): void
    {
        //
    }

    /**
     * Handle the Participation "deleted" event.
     */
    public function deleted(Participation $participation): void
    {
        //
    }

    /**
     * Handle the Participation "restored" event.
     */
    public function restored(Participation $participation): void
    {
        //
    }

    /**
     * Handle the Participation "force deleted" event.
     */
    public function forceDeleted(Participation $participation): void
    {
        //
    }
}
