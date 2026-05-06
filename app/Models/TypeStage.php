<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'description',
        'actif',
        'archived_at'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Rôle : Gestion des types de stage
     * Responsabilités :
     * - Définition des types de stage (entreprise, PFE, initiation, etc.)
     * - Gestion de l'activation/archivage
     * - Relation avec les offres de stage
     */

    // Relations
    public function offres()
    {
        return $this->hasMany(OffreStage::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true)->whereNull('archived_at');
    }

    public function scopeArchive($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // Méthodes métier
    public function archiver()
    {
        $this->archived_at = now();
        $this->save();
    }

    public function desarchiver()
    {
        $this->archived_at = null;
        $this->save();
    }

    public function activer()
    {
        $this->actif = true;
        $this->save();
    }

    public function desactiver()
    {
        $this->actif = false;
        $this->save();
    }

    public function estActif()
    {
        return $this->actif && is_null($this->archived_at);
    }

    public function estArchive()
    {
        return !is_null($this->archived_at);
    }
}
