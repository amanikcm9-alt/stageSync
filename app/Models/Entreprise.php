<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'secteur_activite',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'telephone',
        'email',
        'site_web',
        'logo_path',
        'conditions_stage',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Rôle : Gestion des informations entreprises partenaires
     * Responsabilités :
     * - Stocker les informations complètes des entreprises
     * - Gérer les conditions internes de stage
     * - Lier les entreprises aux offres de stage
     * - Activer/désactiver les entreprises partenaires
     */

    // Relations
    public function offres()
    {
        return $this->hasMany(OffreStage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Accesseurs
    public function getAdresseCompleteAttribute()
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}, {$this->pays}";
    }

    public function getSiteWebUrlAttribute()
    {
        return $this->site_web ? 
            (strpos($this->site_web, 'http') === 0 ? $this->site_web : 'https://' . $this->site_web) 
            : null;
    }

    public function getNombreOffresActivesAttribute()
    {
        return $this->offres()->publiee()->count();
    }
}
