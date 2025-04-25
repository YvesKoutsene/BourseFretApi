<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporteur extends Model
{
    use HasFactory;
    protected $table = 'transporteur';


    protected $fillable = [
        'keytransporteur', 'nom', 'prenom', 'email', 'raisonsociale', 'typetransporteur',
        'contact', 'adresse', 'statut', 'createdby', 'updatedby'
    ];


}
