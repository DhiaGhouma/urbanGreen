<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'commentaire',
        'statut',
        'date_update',
    ];

    /**
     * Relation avec le signalement parent
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
