<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GreenSpacePlant extends Model
{
    use HasFactory;

    protected $fillable = [
        'green_space_id',
        'name',
        'species',
        'quantity',
        'planted_at',
        'maintenance',
        'status',
        'notes',
    ];

    // Ajouter ceci pour que planted_at soit traité comme une date/Carbon
    protected $casts = [
        'planted_at' => 'datetime',
    ];

    public function greenSpace(): BelongsTo
    {
        return $this->belongsTo(GreenSpace::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'en vie' => 'badge-success',
            'malade' => 'badge-warning',
            'abattu' => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}
