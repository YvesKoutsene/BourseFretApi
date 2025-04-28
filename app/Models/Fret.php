<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fret extends Model
{
    use HasFactory;

    protected $table = 'fret';

    protected $fillable = [
        'keyfret', 'raisonannulation', 'jourchargement', 'jourdechargement', 'naturemarchandise', 'poidsmarchandise',
        'poidsrestant', 'nombreconteneurs', 'nombrecamions', 'commentaire', 'boursepulication', 'debutpublication',
        'finpublication', 'numerodossier', 'isdemande', 'documentsupplementaire', 'numerofret', 'photofret', 'idlieuchargement',
        'idlieudechargement', 'idclient', 'idparametresvehicule', 'idtypevehicule', 'idtypemarchandise','statut', 'createdby', 'updatedby'
   ];

   public function lieuchargement()
   {
       return $this->belongsTo(Lieu::class, 'idlieuchargement');
   }

   public function lieudechargement()
   {
       return $this->belongsTo(Lieu::class, 'idlieudechargement');
   }

   public function typemarchandise()
   {
       return $this->belongsTo(Typemarchandise::class, 'idtypemarchandise');
   }

   public function typevehicule()
   {
        return $this->belongsTo(Typevehicule::class, 'idtypevehicule');
   }

   public function parametresvehicule()
   {
        return $this->belongsTo(Parametresvehicules::class, 'idparametresvehicule');
   }

   public function client()
   {
        return $this->belongsTo(Client::class, 'idclient');
   }

}
