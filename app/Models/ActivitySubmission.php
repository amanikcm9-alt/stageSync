<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivitySubmission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $fillable = [
        'activity_id',
        'stagiaire_id',
        'description_travail',
        'commentaires_stagiaire',
        'statut',
        'note',
        'feedback_encadrant',
        'justification_refus',
        'fichiers_joints',
        'date_soumission',
        'date_evaluation',
    ];

    protected $casts = [
        'fichiers_joints' => 'array',
        'note' => 'integer',
        'date_soumission' => 'datetime',
        'date_evaluation' => 'datetime',
    ];

    // Relations
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    // Scopes
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeSoumis($query)
    {
        return $query->where('statut', 'soumis');
    }

    public function scopeEnEvaluation($query)
    {
        return $query->where('statut', 'en_evaluation');
    }

    // Accesseurs
    public function getStatutLabelAttribute()
    {
        $labels = [
            'brouillon' => 'Brouillon',
            'soumis' => 'Soumis',
            'en_evaluation' => 'En évaluation',
            'valide' => 'Validé',
            'refuse' => 'Refusé',
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'brouillon' => 'secondary',
            'soumis' => 'warning',
            'en_evaluation' => 'info',
            'valide' => 'success',
            'refuse' => 'danger',
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function getNoteEtoilesAttribute()
    {
        if ($this->note === null) return null;
        
        $etoiles = '';
        for ($i = 1; $i <= 5; $i++) {
            $noteEtoile = $this->note / 4; // Convertir 0-20 en 0-5 étoiles
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

    // Méthodes métier
    public function soumettre()
    {
        $this->update([
            'statut' => 'soumis',
            'date_soumission' => now(),
        ]);
        
        // Mettre à jour le statut de l'activité
        $this->activity->soumettre();
    }

    public function mettreEnEvaluation()
    {
        $this->update([
            'statut' => 'en_evaluation',
            'date_evaluation' => now(),
        ]);
    }

    public function valider($note = null, $feedback = null)
    {
        $this->update([
            'statut' => 'valide',
            'note' => $note,
            'feedback_encadrant' => $feedback,
            'date_evaluation' => now(),
        ]);
        
        // Mettre à jour le statut de l'activité
        $this->activity->valider();
    }

    public function refuser($justification = null)
    {
        $this->update([
            'statut' => 'refuse',
            'justification_refus' => $justification,
            'date_evaluation' => now(),
        ]);
        
        // Mettre à jour le statut de l'activité
        $this->activity->refuser($justification);
    }

    public function ajouterFichier($filePath)
    {
        $fichiers = $this->fichiers_joints ?? [];
        $fichiers[] = $filePath;
        $this->update(['fichiers_joints' => $fichiers]);
    }

    public function supprimerFichier($filePath)
    {
        $fichiers = $this->fichiers_joints ?? [];
        $fichiers = array_diff($fichiers, [$filePath]);
        $this->update(['fichiers_joints' => array_values($fichiers)]);
    }

    public function estEvalue()
    {
        return in_array($this->statut, ['valide', 'refuse']);
    }

    public function peutEtreModifie()
    {
        return in_array($this->statut, ['brouillon']);
    }
}
