<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'type',
        'fichier_path',
        'url',
        'statut',
        'uploaded_by',
        'activity_id',
        'offre_stage_id',
        'lectures',
    ];

    protected $casts = [
        'lectures' => 'array',
    ];

    // Relations
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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

    public function scopePublies($query)
    {
        return $query->where('statut', 'publie');
    }

    public function scopeReglements($query)
    {
        return $query->where('type', 'reglement');
    }

    public function scopeSupports($query)
    {
        return $query->whereIn('type', ['support', 'documentation']);
    }

    // Accesseurs
    public function getTypeLabelAttribute()
    {
        $labels = [
            'reglement' => 'Règlement',
            'support' => 'Support',
            'livrable' => 'Livrable',
            'fiche_jointe' => 'Fiche jointe',
            'documentation' => 'Documentation',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'reglement' => 'fa-gavel',
            'support' => 'fa-book',
            'livrable' => 'fa-file-alt',
            'fiche_jointe' => 'fa-paperclip',
            'documentation' => 'fa-folder-open',
        ];
        
        return $icons[$this->type] ?? 'fa-file';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'reglement' => 'danger',
            'support' => 'primary',
            'livrable' => 'success',
            'fiche_jointe' => 'info',
            'documentation' => 'warning',
        ];
        
        return $colors[$this->type] ?? 'secondary';
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'publie' => 'Publié',
            'brouillon' => 'Brouillon',
            'archive' => 'Archivé',
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'publie' => 'success',
            'brouillon' => 'secondary',
            'archive' => 'dark',
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function getLienAttribute()
    {
        if ($this->fichier_path) {
            return asset('storage/' . $this->fichier_path);
        }
        
        return $this->url;
    }

    public function getNomFichierAttribute()
    {
        if ($this->fichier_path) {
            return basename($this->fichier_path);
        }
        
        return null;
    }

    public function getExtensionAttribute()
    {
        if ($this->fichier_path) {
            return pathinfo($this->fichier_path, PATHINFO_EXTENSION);
        }
        
        return null;
    }

    // Méthodes métier
    public function marquerCommeLu($userId)
    {
        $lectures = $this->lectures ?? [];
        
        // Supprimer les anciennes lectures de cet utilisateur
        $lectures = array_filter($lectures, function($lecture) use ($userId) {
            return $lecture['user_id'] != $userId;
        });
        
        // Ajouter la nouvelle lecture
        $lectures[] = [
            'user_id' => $userId,
            'date_lecture' => now()->toISOString(),
        ];
        
        $this->update(['lectures' => array_values($lectures)]);
    }

    public function estLu($userId)
    {
        $lectures = $this->lectures ?? [];
        
        foreach ($lectures as $lecture) {
            if ($lecture['user_id'] == $userId) {
                return true;
            }
        }
        
        return false;
    }

    public function getDateLecture($userId)
    {
        $lectures = $this->lectures ?? [];
        
        foreach ($lectures as $lecture) {
            if ($lecture['user_id'] == $userId) {
                return $lecture['date_lecture'];
            }
        }
        
        return null;
    }

    public function publier()
    {
        $this->update(['statut' => 'publie']);
    }

    public function archiver()
    {
        $this->update(['statut' => 'archive']);
    }

    public function estTelechargeable()
    {
        return !is_null($this->fichier_path);
    }

    public function estAccessible()
    {
        return $this->statut === 'publie' && ($this->fichier_path || $this->url);
    }

    public function getTailleFichierAttribute()
    {
        if ($this->fichier_path && file_exists(storage_path('app/public/' . $this->fichier_path))) {
            $bytes = filesize(storage_path('app/public/' . $this->fichier_path));
            
            if ($bytes >= 1073741824) {
                return number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 2) . ' KB';
            } elseif ($bytes > 1) {
                return $bytes . ' bytes';
            } elseif ($bytes == 1) {
                return '1 byte';
            } else {
                return '0 bytes';
            }
        }
        
        return null;
    }
}
