<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{

use Notifiable;
    use HasFactory;

    protected $fillable = ['nom','prenom','email','password','role_id','encadrant_id','active'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Pour accéder à l’encadrant du stagiaire
    public function encadrant(){
        return $this->belongsTo(User::class,'encadrant_id');
    }

    // Pour accéder aux stagiaires d'un encadrant
    public function stagiaires(){
        return $this->hasMany(User::class, 'encadrant_id');
    }

    // Pour accéder à l'encadrant faculté
    public function encadrant_faculte(){
        return $this->belongsTo(User::class,'encadrant_faculte_id');
    }

    // Pour accéder à l'encadrant entreprise
    public function encadrant_entreprise(){
        return $this->belongsTo(User::class,'encadrant_entreprise_id');
    }

    // Pour accéder à l'offre de stage
    public function offre_stage(){
        return $this->belongsTo(OffreStage::class,'offre_stage_id');
    }

    // Pour accéder aux activités de l'utilisateur
    public function activities(){
        return $this->hasMany(Activity::class, 'user_id');
    }

    // Pour accéder aux discussions de l'utilisateur
    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'sender_id')->orWhere('receiver_id', $this->id);
    }

    // Pour accéder aux discussions non lues
    public function unreadDiscussions()
    {
        return Discussion::where('receiver_id', $this->id)->where('read', false)->get();
    }
}