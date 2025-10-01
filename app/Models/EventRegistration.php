<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'statut',
        'commentaire',
        'date_inscription',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
    ];

    /**
     * Get the event that owns the registration.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns the registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'confirmee' => 'success',
            'annulee' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status display text.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'annulee' => 'Annulée',
            default => 'Inconnu',
        };
    }

    /**
     * Check if registration can be confirmed.
     */
    public function canBeConfirmed(): bool
    {
        return $this->statut === 'en_attente' && !$this->event->isFull();
    }

    /**
     * Check if registration can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->statut, ['en_attente', 'confirmee']) && 
               $this->event->date_debut->isFuture();
    }
}