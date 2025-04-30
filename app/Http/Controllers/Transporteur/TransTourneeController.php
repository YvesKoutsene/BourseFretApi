<?php

namespace App\Http\Controllers\Transporteur;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use App\Models\Chauffeur;
use Illuminate\Http\Request;
use App\Models\Fret;
use App\Models\Tournee;
use App\Models\Transporteur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransTourneeController extends Controller
{
    // Fonction permettant de renvoyer les tournées d'un fret
    public function index(Request $request, $key)
    {
        // Vérifiez si l'utilisateur est authentifié (commenté ici)
        /*if (!$request->user()) {
        return response()->json(null, 401); // Non authentifié
        }*/

        // Récupérer le fret en utilisant le keyfret
        $fret = Fret::with(['lieuchargement', 'lieudechargement'])->where('keyfret', $key)->first();

        if (!$fret) {
            return response()->json(null, 404); // Non trouvé
        }

        // Récupérer toutes les tournées associées au fret        
        $tournees = Tournee::with(['lieuDepart', 'lieuArrivee', 'derniereEtape', 'camionActif', 'chauffeurActif'])
            ->where('idfret', $fret->id)
            ->get();

        // Retourner les données sous forme de JSON
        return response()->json([
            'tournees' => $tournees, // Peut être vide
            'fret' => $fret
        ], 200); // Ok
    }


    // Fonction permettant de renvoyer les camions et chauffeurs disponible pour un transporteur
    public function getDisponibilitesTransporteur(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $transporteur = Transporteur::where('keytransporteur', $key)->first();

        if (!$transporteur) {
            return response()->json(null, 404); // Transporteur non trouvé
        }

        // Camions disponibles : statut = 10
        $camions = Camion::where('idtransporteur', $transporteur->id)
            ->where('statut', 10)
            ->get();

        // Chauffeurs disponibles : statut = 10
        $chauffeurs = Chauffeur::where('idtransporteur', $transporteur->id)
            ->where('statut', 10)
            ->get();

        return response()->json([
            'camions' => $camions,
            'chauffeurs' => $chauffeurs
        ], 200); // Ok
    }

    // Fonction d'enrégistrement d'une tournée d'un fret
    public function store(Request $request, $key)
    {

        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        // Étape 1 : Récupérer le fret
        $fret = Fret::where('keyfret', $key)->first();
        if (!$fret) {
            return response()->json(['message' => 'Fret introuvable'], 404);
        }

        $request->validate([
            'idcamion' => 'required|exists:camion,id',
            'idchauffeur' => 'required|exists:chauffeur,id',
            'poids' => 'required|string',
            'numerobl' => 'required|string',
            'numeroconteneur' => 'required|string',
            'idlieudepart' => 'required|exists:lieu,id',
            'idlieuarrivee' => 'required|exists:lieu,id',
            'datedepart' => 'required|date',
            'datearrivee' => 'required|date',
        ]);

        // Étape 2 : Vérifier le nombre maximal de tournées
        $nbTourneesExistantes = Tournee::where('idfret', $fret->id)->count();
        if ($nbTourneesExistantes >= $fret->nombrecamions) {
            return response()->json(['message' => 'Nombre maximal de tournées atteint pour ce fret'], 400);
        }

        // Étape 3 : Vérifier disponibilité du camion
        $camion = Camion::where('id', $request->idcamion)->where('statut', 10)->first();
        if (!$camion) {
            return response()->json(['message' => 'Camion non disponible'], 400);
        }

        // Étape 4 : Vérifier disponibilité du chauffeur
        $chauffeur = Chauffeur::where('id', $request->idchauffeur)->where('statut', 10)->first();
        if (!$chauffeur) {
            return response()->json(['message' => 'Chauffeur non disponible'], 400);
        }

        // Étape 5 : Créer la tournée
        $tournee = new Tournee();
        $tournee->keytournee = Str::uuid()->toString();
        $tournee->idfret = $fret->id;
        $tournee->idlieudepart_ = $request->idlieudepart;
        $tournee->idlieuarrivee_ = $request->idlieuarrivee;
        $tournee->datedepart = $request->datedepart;
        $tournee->datearrivee = $request->datearrivee;
        $tournee->poids = $request->poids;
        $tournee->numerobl = $request->numerobl;
        $tournee->numeroconteneur = $request->numeroconteneur;
        $tournee->save();

        // Étape 6 : Mettre à jour les statuts
        $camion->statut = 20;
        $camion->save();

        $chauffeur->statut = 20;
        $chauffeur->save();

        // Étape 7 : Remplir les tables pivot avec clés uniques
        DB::table('camionstournees')->insert([
            'keycamionstournee' => Str::uuid()->toString(),
            'idcamion' => $camion->id,
            'idtournee' => $tournee->id,
            'statut' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('chauffeurstournee')->insert([
            'keychauffeurstournee' => Str::uuid()->toString(),
            'idchauffeur' => $chauffeur->id,
            'idtournee' => $tournee->id,
            'statut' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Tournée créée avec succès',
            'tournee' => $tournee
        ], 201);
    }
}
