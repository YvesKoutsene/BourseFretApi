<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable
{
    protected $table = 'utilisateur';

    use HasFactory, HasApiTokens, Notifiable;
    protected $fillable = [
        'keyutilisateur', 'nom', 'prenom', 'email', 'telephone', 'adresse',
        'motdepasse', 'access_token', 'idprofil', 'idindicatif', 'idpays',
        'idclient', 'idtransporteur','statut', 'createdby', 'updatedby'
    ];

    public function profil() {
        return $this->belongsTo(Profil::class, 'idprofil');
    }

    public function pays() {
        return $this->belongsTo(Pays::class, 'idpays');
    }

    public function indicatif() {
        return $this->belongsTo(Pays::class, 'idindicatif');
    }

    public function client() {
        return $this->belongsTo(Client::class, 'idclient');
    }

    public function transporteur() {
        return $this->belongsTo(Transporteur::class, 'idtransporteur');
    }

}
