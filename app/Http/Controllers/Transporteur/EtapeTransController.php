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

        $tournee->statut = 20;
        $tournee->save();

        return response()->json([
            'tournee' => $tournee,
        ], 200); // Ok
    }

    // Fonction permettant de clôturer une tournée 
    public function cloturerTournee(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $tournee = Tournee::where('keytournee', $key)->first();

        if (!$tournee) {
            return response()->json(null, 404); // Tournée non trouvée
        }

        if ($tournee->statut !== 20) {
            return response()->json([
                'message' => 'Cette tournée est déjà clôturée ou n’est pas en cours.',
            ], 400);
        }

        // Création de la dernière étape
        Etape::create([
            'keyetape'     => Str::uuid()->toString(),
            'position'     => $validated['position'],
            'dateposition' => now(),
            'latitude'     => $validated['latitude'],
            'longitude'    => $validated['longitude'],
            'statut'       => 10,
            'idtournee'    => $tournee->id,
        ]);

        // Changement du statut de la tournée à 30 (clôturée)
        $tournee->statut = 30;
        $tournee->save();

        // Récupération du camion actif via la relation pivot (statut 10)
        $camion = $tournee->camionActif()->first();
        if ($camion) {
            $camion->statut = 10; 
            $camion->save();
        }

        // Récupération du chauffeur actif via la relation pivot (statut 10)
        $chauffeur = $tournee->chauffeurActif()->first();
        if ($chauffeur) {
            $chauffeur->statut = 10; 
            $chauffeur->save();
        }

        return response()->json([
            'tournee' => $tournee,
        ], 200);
    }

    // Fonction permattant d'afficher les étapes d'une tournée
    public function index(Request $request, $key){

        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $tournee = Tournee::where('keytournee', $key)->first();

        if (!$tournee) {
            return response()->json(null, 404); // Tournée non trouvée
        }
        
        // Récupérer toutes les étapes de la tournée 
        $etapes = $tournee->etapes()->get();

        return response()->json([
            'etapes' => $etapes,
        ], 200); // Ok


    }



}
