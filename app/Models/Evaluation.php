<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stagiaire_id',
        'encadrant_id',
        'activity_id',
        'offre_stage_id',
        'type',
        'note_competence',
        'note_travail',
        'note_attitude',
        'note_globale',
        'appreciation',
        'points_forts',
        'points_amelioration',
        'commentaires',
        'date_evaluation',
        'statut',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'note_competence' => 'integer',
        'note_travail' => 'integer',
        'note_attitude' => 'integer',
        'note_globale' => 'integer',
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

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function offreStage()
    {
        return $this->belongsTo(OffreStage::class, 'offre_stage_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForStagiaire($query, $stagiaireId)
    {
        return $query->where('stagiaire_id', $stagiaireId);
    }

    public function scopeForEncadrant($query, $encadrantId)
    {
        return $query->where('encadrant_id', $encadrantId);
    }

    public function scopeFinalisees($query)
    {
        return $query->where('statut', 'finalisee');
    }

    public function scopeValidees($query)
    {
        return $query->where('statut', 'validee');
    }

    // Accesseurs
    public function getTypeLabelAttribute()
    {
        $labels = [
            'activite' => 'Activité',
            'generale' => 'Générale',
            'finale' => 'Finale',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'activite' => 'primary',
            'generale' => 'info',
            'finale' => 'success',
        ];
        
        return $colors[$this->type] ?? 'secondary';
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'brouillon' => 'Brouillon',
            'finalisee' => 'Finalisée',
            'validee' => 'Validée',
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'brouillon' => 'secondary',
            'finalisee' => 'warning',
            'validee' => 'success',
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function getNoteEtoilesAttribute()
    {
        if ($this->note_globale === null) return null;
        
        $etoiles = '';
        for ($i = 1; $i <= 5; $i++) {
            $noteEtoile = $this->note_globale / 4; // Convertir 0-20 en 0-5 étoiles
            if ($i <= $noteEtoile) {
                $etoiles .= ' <i class="fas fa-star text-warning"></i>';
            } elseif ($i - 0.5 <= $noteEtoile) {
                $etoiles .= ' <i class="fas fa-star-half-alt text-warning"></i>';
            } else {
                $etoiles .= ' <i class="far fa-star text-warning"></i>';
            }
        }
        return $etoiles;
    }

    public function getMoyenneAttribute()
    {
        $notes = [];
        
        if ($this->note_competence !== null) $notes[] = $this->note_competence;
        if ($this->note_travail !== null) $notes[] = $this->note_travail;
        if ($this->note_attitude !== null) $notes[] = $this->note_attitude;
        
        if (empty($notes)) return null;
        
        return round(array_sum($notes) / count($notes), 1);
    }

    public function getAppreciationCourteAttribute()
    {
        return strlen($this->appreciation) > 100 
            ? substr($this->appreciation, 0, 100) . '...' 
            : $this->appreciation;
    }

    // Méthodes métier
    public function calculerNoteGlobale()
    {
        $moyenne = $this->moyenne;
        
        if ($moyenne !== null) {
            $this->update(['note_globale' => round($moyenne)]);
        }
    }

    public function finaliser()
    {
        $this->calculerNoteGlobale();
        $this->update(['statut' => 'finalisee']);
    }

    public function valider()
    {
        if ($this->statut !== 'finalisee') {
            $this->finaliser();
        }
        
        $this->update(['statut' => 'validee']);
    }

    public function estPositive()
    {
        return $this->note_globale >= 10;
    }

    public function estExcellente()
    {
        return $this->note_globale >= 16;
    }

    public function estInsuffisante()
    {
        return $this->note_globale < 10;
    }

    public static function getMoyenneGenerale($stagiaireId, $type = null)
    {
        $query = self::forStagiaire($stagiaireId)->validees();
        
        if ($type) {
            $query->byType($type);
        }
        
        $evaluations = $query->get();
        
        if ($evaluations->isEmpty()) return null;
        
        $total = $evaluations->sum(function($evaluation) {
            return $evaluation->note_globale;
        });
        
        return round($total / $evaluations->count(), 1);
    }

    public static function getDerniereEvaluation($stagiaireId, $type = null)
    {
        $query = self::forStagiaire($stagiaireId)->validees()->latest();
        
        if ($type) {
            $query->byType($type);
        }
        
        return $query->first();
    }
}
