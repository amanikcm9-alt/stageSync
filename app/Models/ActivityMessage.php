<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'expediteur_id',
        'destinataire_id',
        'message',
        'type_message',
        'statut',
        'date_envoi',
        'date_lecture',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'date_lecture' => 'datetime',
    ];

    // Relations
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Scopes
    public function scopeForActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('expediteur_id', $userId)
              ->orWhere('destinataire_id', $userId);
        });
    }

    public function scopeNonLus($query)
    {
        return $query->whereNull('date_lecture');
    }

    public function scopeLus($query)
    {
        return $query->whereNotNull('date_lecture');
    }

    public function scopeDemandesInformation($query)
    {
        return $query->where('type_message', 'demande_information');
    }

    public function scopeReponses($query)
    {
        return $query->where('type_message', 'reponse');
    }

    // Accesseurs
    public function getTypeMessageLabelAttribute()
    {
        $labels = [
            'demande_information' => 'Demande d\'information',
            'reponse' => 'Réponse',
            'information' => 'Information',
        ];
        
        return $labels[$this->type_message] ?? $this->type_message;
    }

    public function getStatutLabelAttribute()
    {
        return $this->date_lecture ? 'Lu' : 'Non lu';
    }

    public function getStatutColorAttribute()
    {
        return $this->date_lecture ? 'success' : 'warning';
    }

    // Méthodes métier
    public function marquerCommeLu()
    {
        $this->update(['date_lecture' => now()]);
    }

    public function estLu()
    {
        return $this->date_lecture !== null;
    }

    public function estNonLu()
    {
        return $this->date_lecture === null;
    }

    public function estDemandeInformation()
    {
        return $this->type_message === 'demande_information';
    }

    public function estReponse()
    {
        return $this->type_message === 'reponse';
    }

    public function estExpediteur($userId)
    {
        return $this->expediteur_id === $userId;
    }

    public function estDestinataire($userId)
    {
        return $this->destinataire_id === $userId;
    }

    public function peutEtreLuPar($userId)
    {
        return $this->destinataire_id === $userId && !$this->estLu();
    }

    // Méthode statique pour créer une demande d'information
    public static function creerDemandeInformation($activityId, $stagiaireId, $encadrantId, $message)
    {
        return self::create([
            'activity_id' => $activityId,
            'expediteur_id' => $stagiaireId,
            'destinataire_id' => $encadrantId,
            'message' => $message,
            'type_message' => 'demande_information',
            'statut' => 'envoye',
            'date_envoi' => now(),
        ]);
    }

    // Méthode statique pour créer une réponse
    public static function creerReponse($activityId, $encadrantId, $stagiaireId, $message)
    {
        return self::create([
            'activity_id' => $activityId,
            'expediteur_id' => $encadrantId,
            'destinataire_id' => $stagiaireId,
            'message' => $message,
            'type_message' => 'reponse',
            'statut' => 'envoye',
            'date_envoi' => now(),
        ]);
    }
}
