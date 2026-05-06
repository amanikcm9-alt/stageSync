<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidature_id',
        'date_entretien',
        'heure_entretien',
        'lieu_entretien',
        'notes_entretien',
        'note_evaluation',
        'commentaires_evaluation',
        'statut',
        'evaluated_by',
        'evaluated_at'
    ];

    protected $casts = [
        'date_entretien' => 'date',
        'heure_entretien' => 'datetime',
        'note_evaluation' => 'decimal:2',
        'evaluated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Statuts possibles
    const STATUT_PLANIFIE = 'planifie';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';

    // Relations
    public function candidature()
    {
        return $this->belongsTo(Candidature::class);
    }

    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    // Scopes
    public function scopePlanifie($query)
    {
        return $query->where('statut', self::STATUT_PLANIFIE);
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', self::STATUT_TERMINE);
    }

    public function scopeNonEvalue($query)
    {
        return $query->whereNull('evaluated_at');
    }

    public function scopeEvalue($query)
    {
        return $query->whereNotNull('evaluated_at');
    }

    // Méthodes
    public function isPlanifie()
    {
        return $this->statut === self::STATUT_PLANIFIE;
    }

    public function isTermine()
    {
        return $this->statut === self::STATUT_TERMINE;
    }

    public function isEvalue()
    {
        return !is_null($this->evaluated_at);
    }

    public function peutEtreEvalue()
    {
        return $this->isTermine() && !$this->isEvalue();
    }

    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_PLANIFIE => 'Planifié',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_ANNULE => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function getNoteLabelAttribute()
    {
        if (is_null($this->note_evaluation)) {
            return 'Non évalué';
        }
        
        if ($this->note_evaluation >= 16) {
            return 'Excellent';
        } elseif ($this->note_evaluation >= 14) {
            return 'Très bien';
        } elseif ($this->note_evaluation >= 12) {
            return 'Bien';
        } elseif ($this->note_evaluation >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
}
