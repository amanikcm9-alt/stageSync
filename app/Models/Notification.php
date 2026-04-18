<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'destinataire',
        'sujet',
        'contenu',
        'statut',
        'erreur_message',
        'date_envoi',
        'notifiable_type',
        'notifiable_id'
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
    ];

    /**
     * Rôle : Gestion des notifications SMS et emails
     * Responsabilités :
     * - Stocker tous les messages à envoyer (SMS/Email)
     * - Gérer les statuts d'envoi (en attente → envoyé → échec)
     * - Relation polymorphique avec n'importe quel modèle
     * - Traiter les erreurs d'envoi
     */

    // Relation polymorphique
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeSms($query)
    {
        return $query->where('type', 'sms');
    }

    public function scopeEmail($query)
    {
        return $query->where('type', 'email');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnvoye($query)
    {
        return $query->where('statut', 'envoye');
    }

    public function scopeEchec($query)
    {
        return $query->where('statut', 'echec');
    }

    // Accesseurs
    public function getStatutFormateAttribute()
    {
        $statuts = [
            'envoye' => 'Envoyé',
            'en_attente' => 'En attente',
            'echec' => 'Échec'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }

    public function getStatutCouleurAttribute()
    {
        $couleurs = [
            'envoye' => 'success',
            'en_attente' => 'warning',
            'echec' => 'danger'
        ];
        
        return $couleurs[$this->statut] ?? 'secondary';
    }

    public function getTypeFormateAttribute()
    {
        return strtoupper($this->type);
    }

    // Méthodes métier
    public function marquerEnvoye()
    {
        $this->statut = 'envoye';
        $this->date_envoi = now();
        $this->save();
    }

    public function marquerEchec($erreur)
    {
        $this->statut = 'echec';
        $this->erreur_message = $erreur;
        $this->save();
    }

    public function peutEtreEnvoye()
    {
        return $this->statut === 'en_attente';
    }
}
