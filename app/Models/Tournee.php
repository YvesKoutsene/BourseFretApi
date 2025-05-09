<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournee extends Model
{
    use HasFactory;
    protected $table = 'tournee';

    protected $fillable = [
        'keytournee', 'numerotournee','datedepart', 'datearrivee', 'poids', 'numerobl', 'numeroconteneur',
        'idfret', 'idlieudepart', 'idlieuarrivee', 'statut', 'createdby', 'updatedby'
   ];

   public function fret()
   {
        return $this->belongsTo(Fret::class, 'idfret');
   }

   public function lieudepart()
   {
        return $this->belongsTo(Lieu::class, 'idlieudepart');
   }

   public function lieuarrivee()
   {
        return $this->belongsTo(Lieu::class, 'idlieuarrivee');
   }

   public function etapes()
   {
       return $this->hasMany(Etape::class, 'idtournee');
   }

   public function derniereEtape()
   {
       return $this->hasOne(Etape::class, 'idtournee')->latestOfMany(); 
   }

    public function camionActif()
    {
        return $this->belongsToMany(Camion::class, 'camionstournees', 'idtournee', 'idcamion')
            ->withPivot('statut')
            ->wherePivot('statut', 10);  
    }

    public function chauffeurActif()
    {
        return $this->belongsToMany(Chauffeur::class, 'chauffeurstournee', 'idtournee', 'idchauffeur')
            ->withPivot('statut')
            ->wherePivot('statut', 10); 
    }

}
