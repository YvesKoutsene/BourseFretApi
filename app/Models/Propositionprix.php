<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propositionprix extends Model
{
    use HasFactory;
    protected $table = 'propositionprix';

    protected $fillable = [
        'keypropositionprix', 'idfret', 'prix', 'commentaire', 'raisonrefus', 'createdby', 'updatedby', 'statut'
    ];




}
