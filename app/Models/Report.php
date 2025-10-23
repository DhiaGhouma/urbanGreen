<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    // Attributs pouvant être remplis en masse
    protected $fillable = [
        'user_id',
        'green_space_id',
        'description',
        'photo',
        'statut',
        'date_signalement',
        'title',
        'category',
        'priority',
        'latitude',
        'longitude',
        'recommended_action', // ajouté pour l'IA
    ];

    /**
     * Relation avec l'utilisateur qui a créé le signalement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'espace vert concerné
     */
    public function greenSpace(): BelongsTo
    {
        return $this->belongsTo(GreenSpace::class);
    }

    /**
     * Relation avec les mises à jour du signalement
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ReportUpdate::class);
    }

    /**
     * Retourne une classe CSS pour l'affichage du statut
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->statut) {
            'en_attente' => 'badge-secondary',
            'en_cours'   => 'badge-warning',
            'resolu'     => 'badge-success',
            default      => 'badge-light',
        };
    }

    /**
     * Retourne une icône selon la catégorie
     */
    public function getCategoryIcon(): string
    {
        return match ($this->category) {
            'dechets' => '🗑️',
            'plantes_mortes' => '🌿',
            'vandalisme' => '🚨',
            'equipement' => '⚙️',
            'autre' => '❓',
            default => '❓',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            default => 'Statut inconnu',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'faible' => 'badge badge-success',
            'normale' => 'badge badge-primary',
            'élevée' => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'faible'   => 'Faible',
            'normale'  => 'Normale',
            'élevée'   => 'Élevée',
            default    => 'Inconnue',
        };
    }

    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'dechets'         => 'Déchets',
            'plantes'         => 'Plantes',
            'infrastructures' => 'Infrastructures',
            'autre'           => 'Autre',
            default           => 'Inconnue',
        };
    }

    // Nouvelle méthode pour stocker une recommandation d'action
    public function setRecommendedAction(string $action): void
    {
        $this->recommended_action = $action;
        $this->save();
    }

    protected $casts = [
        'date_signalement' => 'datetime',
    ];
}
