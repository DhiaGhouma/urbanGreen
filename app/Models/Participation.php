<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'green_space_id',
        'date',
        'statut',
        'preferences'
    ];

    protected $casts = [
        'date' => 'date',
        'preferences' => 'array',
    ];

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with GreenSpace
    public function greenSpace(): BelongsTo
    {
        return $this->belongsTo(GreenSpace::class);
    }

    // Helper method for status badge styling
    public function getStatusBadgeClass(): string
    {
        return match($this->statut) {
            'en_attente' => 'badge-warning',
            'confirmee' => 'badge-success',
            'annulee' => 'badge-danger',
            'terminee' => 'badge-primary',
            default => 'badge-secondary'
        };
    }

    // Helper method for status display in French
    public function getStatutLabel(): string
    {
        return match($this->statut) {
            'en_attente' => 'En Attente',
            'confirmee' => 'Confirmée',
            'annulee' => 'Annulée',
            'terminee' => 'Terminée',
            default => ucfirst(str_replace('_', ' ', $this->statut))
        };
    }
}
