<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        // Informations personnelles
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        
        // Documents
        'cv_path',
        'lettre_motivation_path',
        'lettre_motivation', // Ajout du champ texte pour la lettre de motivation
        
        // Relations et statut
        'offre_stage_id',
        'statut',
        'date_decision',
        'motif_refus',
        'message',
        'commentaire',
        'archived_at',
        
        // Formation
        'date_naissance',
        'dernier_diplome',
        'etablissement',
        'annee_diplome',
        
        // Entretien
        'date_entretien',
        'heure_entretien',
        'lieu_entretien',
        'notes_entretien',
        
        // Lien vers compte stagiaire si accepté
        'stagiaire_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'annee_diplome' => 'integer',
        'date_decision' => 'date',
        'date_entretien' => 'date',
        'heure_entretien' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime'
    ];

    /**
     * Rôle : Gestion des candidatures de stage
     * Responsabilités :
     * - Stocker toutes les informations du candidat
     * - Gérer le workflow de validation (reçu → en cours → accepté/refusé)
     * - Gérer les documents (CV, lettre, portfolio)
     * - Planifier les entretiens
     * - Créer automatiquement le compte stagiaire si accepté
     */

    // Relations
    public function offreStage()
    {
        return $this->belongsTo(OffreStage::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Scopes pour filtrer par statut
    public function scopeRecue($query)
    {
        return $query->where('statut', 'recue');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeAccepte($query)
    {
        return $query->where('statut', 'accepte');
    }

    public function scopeRefuse($query)
    {
        return $query->where('statut', 'refuse');
    }

    // Accesseurs pour formatage
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance->age;
    }

    public function getStatutFormateAttribute()
    {
        $statuts = [
            'recue' => 'Reçue',
            'en_cours' => 'En cours',
            'accepte' => 'Acceptée',
            'refuse' => 'Refusée'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }

    public function getStatutCouleurAttribute()
    {
        $couleurs = [
            'recue' => 'primary',
            'en_cours' => 'warning',
            'accepte' => 'success',
            'refuse' => 'danger'
        ];
        
        return $couleurs[$this->statut] ?? 'secondary';
    }

    // Méthodes métier
    public function peutEtreTraitee()
    {
        return in_array($this->statut, ['recue', 'en_cours']);
    }

    public function planifierEntretien($dateTime, $lieu = null)
    {
        $this->date_entretien = $dateTime;
        $this->lieu_entretien = $lieu;
        $this->statut = 'en_cours';
        $this->save();
        
        // Envoyer notification SMS
        $this->envoyerNotificationSMS('entretien_planifie');
    }

    public function accepter($motif = null)
    {
        $this->statut = 'accepte';
        $this->motif_refus = null;
        $this->save();
        
        // Créer le compte stagiaire
        $this->creerCompteStagiaire();
        
        // Envoyer notification SMS
        $this->envoyerNotificationSMS('candidature_acceptee');
    }

    public function refuser($motif)
    {
        $this->statut = 'refuse';
        $this->motif_refus = $motif;
        $this->save();
        
        // Envoyer notification SMS
        $this->envoyerNotificationSMS('candidature_refusee');
    }

    private function creerCompteStagiaire()
    {
        // Générer un mot de passe temporaire
        $password = 'stagiaire' . rand(1000, 9999);
        
        $stagiaire = User::create([
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'password' => bcrypt($password),
            'role_id' => 4, // Rôle stagiaire
            'email_verified_at' => now()
        ]);
        
        // Lier la candidature au nouveau stagiaire
        $this->stagiaire_id = $stagiaire->id;
        $this->save();
        
        return $stagiaire;
    }

    private function envoyerNotificationSMS($type)
    {
        // Créer la notification en base
        $templates = [
            'entretien_planifie' => [
                'sujet' => 'Entretien planifié',
                'contenu' => "Bonjour {$this->prenom}, votre entretien est prévu le {$this->date_entretien->format('d/m/Y H:i')}. Lieu: {$this->lieu_entretien}"
            ],
            'candidature_acceptee' => [
                'sujet' => 'Candidature acceptée',
                'contenu' => "Félicitations {$this->prenom} ! Votre candidature a été acceptée. Un compte stagiaire a été créé avec votre email."
            ],
            'candidature_refusee' => [
                'sujet' => 'Candidature refusée',
                'contenu' => "Bonjour {$this->prenom}, nous regrettons de vous informer que votre candidature n'a pas été retenue."
            ]
        ];

        $template = $templates[$type];
        
        Notification::create([
            'user_id' => 1, // À adapter selon le système
            'sujet' => $template['sujet'],
            'contenu' => $template['contenu'],
            'type' => 'sms',
            'destinataire' => $this->telephone,
            'statut' => 'envoye',
            'date_envoi' => now()
        ]);
    }

    /**
     * Scope pour récupérer uniquement les candidatures non archivées
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope pour récupérer uniquement les candidatures archivées
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Vérifier si la candidature est archivée
     */
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    /**
     * Archiver la candidature
     */
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Désarchiver la candidature
     */
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }

    /**
     * Scope pour filtrer par statut d'archivage
     */
    public function scopeByArchiveStatus($query, $archived = false)
    {
        if ($archived) {
            return $query->whereNotNull('archived_at');
        }
        return $query->whereNull('archived_at');
    }
}
