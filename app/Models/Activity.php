<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'objectifs',
        'statut',
        'priorite',
        'date_debut',
        'date_fin',
        'date_limite',
        'livrables_attendus',
        'commentaires',
        'progression',
        'encadrant_id',
        'stagiaire_id',
        'offre_stage_id',
        'date_soumission',
        'date_validation',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_limite' => 'date',
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
        'progression' => 'integer',
    ];

    // Relations
    public function encadrant()
    {
        return $this->belongsTo(User::class, 'encadrant_id');
    }

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    public function offreStage()
    {
        return $this->belongsTo(OffreStage::class, 'offre_stage_id');
    }

    public function submissions()
    {
        return $this->hasMany(ActivitySubmission::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class)->latest();
    }

    public function latestDiscussions()
    {
        return $this->discussions()->take(10);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // Scopes
    public function scopeForEncadrant($query, $encadrantId)
    {
        return $query->where('encadrant_id', $encadrantId);
    }

    public function scopeForStagiaire($query, $stagiaireId)
    {
        return $query->where('stagiaire_id', $stagiaireId);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeByPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    // Accesseurs
    public function getStatutLabelAttribute()
    {
        $labels = [
            'proposee' => 'Proposée',
            'assignee' => 'Assignée',
            'en_cours' => 'En cours',
            'soumise' => 'Soumise',
            'validee' => 'Validée',
            'refusee' => 'Refusée',
            'terminee' => 'Terminée',
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getPrioriteLabelAttribute()
    {
        $labels = [
            'basse' => 'Basse',
            'moyenne' => 'Moyenne',
            'haute' => 'Haute',
            'urgente' => 'Urgente',
        ];
        
        return $labels[$this->priorite] ?? $this->priorite;
    }

    public function getPrioriteColorAttribute()
    {
        $colors = [
            'basse' => 'success',
            'moyenne' => 'info',
            'haute' => 'warning',
            'urgente' => 'danger',
        ];
        
        return $colors[$this->priorite] ?? 'secondary';
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'proposee' => 'secondary',
            'assignee' => 'primary',
            'en_cours' => 'info',
            'soumise' => 'warning',
            'validee' => 'success',
            'refusee' => 'danger',
            'terminee' => 'dark',
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function getProgressionPourcentageAttribute()
    {
        return $this->progression . '%';
    }

    // Méthodes métier
    public function assignerAuStagiaire($stagiaireId)
    {
        $this->update([
            'stagiaire_id' => $stagiaireId,
            'statut' => 'assignee',
            'date_debut' => now(),
        ]);
    }

    public function demarrer()
    {
        $this->update([
            'statut' => 'en_cours',
            'date_debut' => $this->date_debut ?? now(),
        ]);
    }

    public function soumettre()
    {
        $this->update([
            'statut' => 'soumise',
            'date_soumission' => now(),
            'progression' => 100,
        ]);
    }

    public function valider()
    {
        $this->update([
            'statut' => 'validee',
            'date_validation' => now(),
        ]);
    }

    public function refuser($justification = null)
    {
        $this->update([
            'statut' => 'refusee',
            'commentaires' => $justification,
        ]);
    }

    public function mettreAJourProgression($progression)
    {
        $this->update([
            'progression' => min(100, max(0, $progression)),
        ]);
    }

    public function estEnRetard()
    {
        return $this->date_limite && now()->greaterThan($this->date_limite) && !in_array($this->statut, ['validee', 'terminee']);
    }

    public function joursRestants()
    {
        if (!$this->date_limite) return null;
        
        $jours = now()->diffInDays($this->date_limite, false);
        return $jours > 0 ? $jours : 0;
    }
}
