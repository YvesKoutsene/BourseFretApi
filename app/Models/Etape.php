<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etape extends Model
{
    use HasFactory;
    protected $table = 'etape';

    protected $fillable = [
        'keyetape', 'position', 'dateposition', 'longitude', 'latitude',
         'idtournee', 'statut', 'createdby', 'updatedby'
    ];

    public function tournee()
    {
        return $this->belongsTo(Tournee::class, 'idtournee');
    }

}
