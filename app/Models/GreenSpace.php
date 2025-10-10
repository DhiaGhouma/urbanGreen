<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class GreenSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'type',
        'complexity_level',
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
            'propos�' => 'badge-primary',
            'en cours' => 'badge-warning',
            'termin�' => 'badge-success',
            default => 'badge-secondary'
        };
    }

    public function getFormattedSurface(): string
    {
        if (!$this->surface) {
            return 'Non d�finie';
        }

        if ($this->surface >= 10000) {
            return number_format($this->surface / 10000, 2) . ' ha';
        }

        return number_format($this->surface, 0) . ' m�';
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(GreenSpacePlant::class);
    }

    public function feedbacks(): HasManyThrough
    {
        return $this->hasManyThrough(
            ParticipationFeedback::class,
            Participation::class,
            'green_space_id',
            'participation_id'
        );
    }

    public function toAIFormat(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description ?? '',
            'activities' => $this->activities ?? [],
            'type' => $this->type ?? '',
            'location' => $this->location ?? '',
            'complexity_level' => $this->complexity_level ?? 'd�butant',
        ];

        if ($this->latitude && $this->longitude) {
            $data['coordinates'] = [
                'lat' => (float) $this->latitude,
                'lon' => (float) $this->longitude,
            ];
        }

        return $data;
    }
}