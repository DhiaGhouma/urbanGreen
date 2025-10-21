<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Association extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'domain'
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    /**
     * Get the events for the association.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}