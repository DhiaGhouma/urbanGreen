<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'type',
        'date_debut',
        'date_fin',
        'lieu',
        'adresse',
        'capacite_max',
        'places_disponibles',
        'statut',
        'association_id',
        'image',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    /**
     * Get the association that owns the event.
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    /**
     * Get the registrations for the event.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get confirmed registrations.
     */
    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('statut', 'confirmee');
    }

    /**
     * Check if event is full.
     */
    public function isFull(): bool
    {
        if (!$this->capacite_max) {
            return false;
        }
        return $this->confirmedRegistrations()->count() >= $this->capacite_max;
    }

    /**
     * Check if user is registered.
     */
    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()->where('user_id', $userId)->exists();
    }

    /**
     * Get available places count.
     */
    public function getAvailablePlacesAttribute(): ?int
    {
        if (!$this->capacite_max) {
            return null;
        }
        $confirmed = $this->confirmedRegistrations()->count();
        return max(0, $this->capacite_max - $confirmed);
    }

    /**
     * Get event type badge color.
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'plantation' => 'success',
            'conference' => 'primary',
            'atelier' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get event status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->statut) {
            'planifie' => 'warning',
            'en_cours' => 'info',
            'termine' => 'secondary',
            'annule' => 'danger',
            default => 'light',
        };
    }

    /**
     * Check if event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->date_debut->isFuture() && $this->statut === 'planifie';
    }

    /**
     * Check if event is past.
     */
    public function isPast(): bool
    {
        return $this->date_debut->isPast();
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_debut', '>', now())
                    ->where('statut', '!=', 'annule')
                    ->orderBy('date_debut', 'asc');
    }

    /**
     * Scope for past events.
     */
    public function scopePast($query)
    {
        return $query->where('date_debut', '<', now())
                    ->orderBy('date_debut', 'desc');
    }

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}