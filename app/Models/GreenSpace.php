<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GreenSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'type',
        'surface',
        'latitude',
        'longitude',
        'status',
        'photos_before',
        'photos_after',
        'activities'
    ];

    protected $casts = [
        'photos_before' => 'array',
        'photos_after' => 'array',
        'activities' => 'array',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
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

    public function getFormattedSurface(): string
    {
        if (!$this->surface) {
            return 'Non définie';
        }

        if ($this->surface >= 10000) {
            return number_format($this->surface / 10000, 2) . ' ha';
        }

        return number_format($this->surface, 0) . ' m²';
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(GreenSpacePlant::class);
    }
}
