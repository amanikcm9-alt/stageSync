<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'objectifs',
        'statut',
        'stagiaire_id',
        'encadrant_id',
        'date_proposition',
        'date_limite',
        'commentaires_encadrant',
        'date_validation',
    ];

    protected $casts = [
        'date_proposition' => 'date',
        'date_limite' => 'date',
        'date_validation' => 'date',
    ];

    // Relations
    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    public function encadrant()
    {
        return $this->belongsTo(User::class, 'encadrant_id');
    }

    // Scopes
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeForStagiaire($query, $stagiaireId)
    {
        return $query->where('stagiaire_id', $stagiaireId);
    }

    public function scopeForEncadrant($query, $encadrantId)
    {
        return $query->where('encadrant_id', $encadrantId);
    }

    // Accesseurs
    public function getStatutLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente de validation',
            'validee' => 'Validée',
            'refusee' => 'Refusée',
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'en_attente' => 'warning',
            'validee' => 'success',
            'refusee' => 'danger',
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    // Méthodes métier
    public function valider($encadrantId = null)
    {
        $this->update([
            'statut' => 'validee',
            'encadrant_id' => $encadrantId ?? auth()->id(),
            'date_validation' => now(),
        ]);

        // Créer l'activité correspondante
        $activity = Activity::create([
            'titre' => $this->titre,
            'description' => $this->description,
            'objectifs' => $this->objectifs,
            'statut' => 'assignee',
            'stagiaire_id' => $this->stagiaire_id,
            'encadrant_id' => $encadrantId ?? auth()->id(),
            'date_proposition' => $this->date_proposition,
            'date_validation' => now(),
        ]);

        return $activity;
    }

    public function refuser($justification = null)
    {
        $this->update([
            'statut' => 'refusee',
            'commentaires_encadrant' => $justification,
            'encadrant_id' => auth()->id(),
        ]);
    }

    public function estEnAttente()
    {
        return $this->statut === 'en_attente';
    }

    public function estValidee()
    {
        return $this->statut === 'validee';
    }

    public function estRefusee()
    {
        return $this->statut === 'refusee';
    }
}
