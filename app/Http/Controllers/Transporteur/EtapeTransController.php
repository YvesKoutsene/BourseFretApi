<?php

namespace App\Http\Controllers\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournee;
use App\Models\Etape;
use Illuminate\Support\Str;

class EtapeTransController extends Controller
{
    // Fonction permettant de démarrer une tournée
    public function demarrerTournee(Request $request, $key)
    {
        // Vérifiez si l'utilisateur est authentifié (commenté ici)
        if (!$request->user()) {
        return response()->json(null, 401); // Non authentifié
        }

        // Validation des données envoyées dans le body
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $tournee = Tournee::where('keytournee', $key)->first();

        if (!$tournee) {
            return response()->json(null, 404); // Tournée non trouvée
        }

        // Vérification du statut : doit être "en attente" (10)
        if ($tournee->statut !== 10) {
            return response()->json([
                'message' => 'Elle est déjà en cours ou clôturée.',
            ], 400);
        }

        $tournee->statut = 20;
        $tournee->save();

        // Création de la première étape
        Etape::create([
            'keyetape'     => Str::uuid()->toString(),
            'position'     => $validated['position'],
            'dateposition' => now(),
            'latitude'    => $validated['latitude'], 
            'longitude'    => $validated['longitude'],
            'statut'       => 10,
            'idtournee'    => $tournee->id,
        ]);

        return response()->json([
            'tournee' => $tournee,
        ], 200); // Ok
    }
}
