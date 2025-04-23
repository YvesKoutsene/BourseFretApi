<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporteur extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyclient', 'nom', 'prenom', 'email', 'raisonsociale', 'typeclient',
        'contact', 'adresse', 'statut'
    ];


}
