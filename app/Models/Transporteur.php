<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporteur extends Model
{
    use HasFactory;
    protected $table = 'transporteur';


    protected $fillable = [
        'keytransporteur',
        'nom',
        'prenom',
        'email',
        'raisonsociale',
        'typetransporteur',
        'contact',
        'adresse',
        'statut',
        'createdby',
        'updatedby'
    ];

    public function fretsAttribues()
    {
        return $this->belongsToMany(Fret::class, 'attributionfret', 'idtransporteur', 'idfret')
            ->where('attributionfret.statut', 10)
            ->orderByDesc('attributionfret.created_at')
            ->whereIn('fret.statut', [30, 40, 50])
            ->with(['lieuchargement', 'lieudechargement', 'typemarchandise', 'typevehicule']);
    }


}
