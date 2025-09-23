<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'estimated_budget',
        'status',
        'association_id',
        'green_space_id'
    ];

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function greenSpace(): BelongsTo
    {
        return $this->belongsTo(GreenSpace::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'proposé' => 'badge-primary',
            'en cours' => 'badge-warning',
            'terminé' => 'badge-success',
            default => 'badge-secondary'
        };
    }
}