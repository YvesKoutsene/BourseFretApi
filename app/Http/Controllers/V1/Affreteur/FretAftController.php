<?php

namespace App\Http\Controllers\V1\Affreteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fret;

class FretAftController extends Controller
{
    // Fonction pour renvoyer les frets introduits 
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $frets = Fret::whereIn('statut', [20,30])
            ->with(['lieuchargement', 'lieudechargement', 'typemarchandise'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($frets->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json([
            'frets' => $frets
        ], 200); // Ok
    }

    // Fonction pour valider un fret (Avec attribution au Transporteur anaxar idtrans = 4)
    public function validation(Request $request, $key){

         if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }
        
    }

}
