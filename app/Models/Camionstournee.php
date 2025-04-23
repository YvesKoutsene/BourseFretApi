<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camionstournee extends Model
{
    use HasFactory;

    protected $table = 'camionstournees';

    protected $fillable = [
        'keycamionstournee', 'idcamion', 'idtournee', 'statut', 'createdby', 'updatedby'
    ];

}
