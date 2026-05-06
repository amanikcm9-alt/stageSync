<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OffreStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'missions',
        'secteur',
        'secteur_id',
        'lieu',
        'duree_semaines',
        'remuneration',
        'statut',
        'type_stage',
        'type_stage_id',
        'date_debut',
        'date_fin',
        'entreprise_id',
        'rh_id'
    ];

    protected $casts = [
        'remuneration' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Rôle : Gestion des offres de stage
     * Responsabilités : 
     * - Définition des offres avec tous les détails
     * - Gestion du cycle de vie (brouillon → publié → clôturé)
     * - Relation avec l'entreprise et le RH créateur
     * - Lien vers toutes les candidatures associées
     */

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function rh()
    {
        return $this->belongsTo(User::class, 'rh_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function typeStage()
    {
        return $this->belongsTo(TypeStage::class);
    }

    // Scopes pour filtrer facilement
    public function scopePubliee($query)
    {
        return $query->where('statut', 'publiee')
                    ->where('statut', '!=', 'affectée');
    }

    public function scopeActive($query)
    {
        return $query->where('statut', 'publiee')
                    ->where('statut', '!=', 'affectée')
                    ->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now());
    }

    // Scopes pour les types de stages
    public function scopeEntreprise($query)
    {
        return $query->where('type_stage', 'entreprise');
    }

    public function scopePfe($query)
    {
        return $query->where('type_stage', 'pfe');
    }

    public function scopeInitiation($query)
    {
        return $query->where('type_stage', 'initiation');
    }

    public function scopePerfectionnement($query)
    {
        return $query->where('type_stage', 'perfectionnement');
    }

    public function scopeBenefolat($query)
    {
        return $query->where('type_stage', 'benefolat');
    }

    public function scopeAcademique($query)
    {
        return $query->whereIn('type_stage', ['pfe', 'initiation', 'perfectionnement']);
    }

    public function scopeRenumere($query)
    {
        return $query->whereNotNull('remuneration')->where('remuneration', '>', 0);
    }

    public function scopeNonRenumere($query)
    {
        return $query->where(function($q) {
            $q->whereNull('remuneration')->orWhere('remuneration', '=', 0);
        });
    }

    // Accesseurs pour formatage
    public function getDureeFormateeAttribute()
    {
        return $this->duree_semaines . ' semaine' . ($this->duree_semaines > 1 ? 's' : '');
    }

    public function getRemunerationFormateeAttribute()
    {
        return $this->remuneration ? number_format($this->remuneration, 2, ',', ' ') . ' €/mois' : 'Non rémunéré';
    }

    public function getTypeStageFormateAttribute()
    {
        $types = [
            'entreprise' => 'Entreprise',
            'pfe' => 'PFE',
            'initiation' => 'Initiation',
            'perfectionnement' => 'Perfectionnement',
            'benefolat' => 'Bénévolat'
        ];
        
        return $types[$this->type_stage] ?? 'Non spécifié';
    }

    // Méthodes métier
    public function estPublieeEtActive()
    {
        return $this->statut === 'publiee' && $this->statut !== 'affectée';
    }

    public function peutEtreCandidatee()
    {
        return $this->estPublieeEtActive();
    }
}
