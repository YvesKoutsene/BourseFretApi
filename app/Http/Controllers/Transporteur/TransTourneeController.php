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
        /*if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }*/

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

        // Validation manuelle des paramètres dans l'URL
        $validator = Validator::make($request->query(), [
            'idcamion' => 'required|exists:camion,id',
            'idchauffeur' => 'required|exists:chauffeur,id',
            'poids' => 'required|numeric',
            'numerobl' => 'required|string',
            'numeroconteneur' => 'required|string',
            'idlieudepart' => 'required|exists:lieu,id',
            'idlieuarrivee' => 'required|exists:lieu,id',
            'datedepart' => 'required|date',
            'datearrivee' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Récupération des données
        $idcamion = $request->query('idcamion');
        $idchauffeur = $request->query('idchauffeur');

        // Étape 2 : Vérifier le nombre maximal de tournées
        $nbTourneesExistantes = Tournee::where('idfret', $fret->id)->count();
        if ($nbTourneesExistantes >= $fret->nombrecamions) {
            return response()->json(['message' => 'Nombre maximal de tournées atteint pour ce fret'], 400);
        }

        // Étape 3 : Vérifier disponibilité du camion
        $camion = Camion::where('id', $idcamion)->where('statut', 10)->first();
        if (!$camion) {
            return response()->json(['message' => 'Camion non disponible'], 400);
        }

        // Étape 4 : Vérifier disponibilité du chauffeur
        $chauffeur = Chauffeur::where('id', $idchauffeur)->where('statut', 10)->first();
        if (!$chauffeur) {
            return response()->json(['message' => 'Chauffeur non disponible'], 400);
        }

        // Étape 5 : Créer la tournée
        $tournee = new Tournee();
        $tournee->keytournee = Str::uuid()->toString();
        $tournee->idfret = $fret->id;
        $tournee->idlieudepart = $request->query('idlieudepart');
        $tournee->idlieuarrivee = $request->query('idlieuarrivee');
        $tournee->datedepart = $request->query('datedepart');
        $tournee->datearrivee = $request->query('datearrivee');
        $tournee->poids = $request->query('poids');
        $tournee->numerobl = $request->query('numerobl');
        $tournee->numeroconteneur = $request->query('numeroconteneur');
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
            'keytournee' => $tournee->keytournee,
            'tournee' => $tournee
        ], 201);
    }
}
