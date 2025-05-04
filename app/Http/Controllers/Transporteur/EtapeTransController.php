<?php

namespace App\Http\Controllers\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournee;
use App\Models\Etape;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    public function index(Request $request, $key)
    {

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
            'tournee' => $tournee,
        ], 200); // Ok

    }

    // Fonction permettant d'enrégistrer les étapes des tournées en cours d'un fret
    public function store(Request $request)
    {
        // Vérifie l'authentification
        if (!$request->user()) {
            return response()->json(null, 401);
        }

        // Validation avancée
        $validator = Validator::make($request->all(), [
            'etapes' => 'required|array|min:1',
            'etapes.*.tournee_id' => 'required|integer',
            'etapes.*.etapes' => 'nullable|array',
            'etapes.*.etapes.*.position' => 'required_with:etapes.*.etapes|string|max:255',
            'etapes.*.etapes.*.latitude' => 'required_with:etapes.*.etapes|numeric',
            'etapes.*.etapes.*.longitude' => 'required_with:etapes.*.etapes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $etapesCreees = [];

        // Optimise la récupération des tournées
        $tourneeIds = collect($request->etapes)->pluck('tournee_id')->unique();
        $tournees = Tournee::whereIn('id', $tourneeIds)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            foreach ($request->etapes as $group) {
                $tourneeId = $group['tournee_id'];
                $tournee = $tournees[$tourneeId] ?? null;

                if (!$tournee) {
                    DB::rollBack();
                    return response()->json(['message' => "Une des tournées est introuvable."], 404);
                }

                if ((int) $tournee->statut !== 20) {
                    DB::rollBack();
                    return response()->json(['message' => "Une des tournées n’est pas en cours."], 400);
                }

                $etapes = $group['etapes'] ?? [];

                foreach ($etapes as $etapeData) {
                    $etapesCreees[] = Etape::create([
                        'idtournee'    => $tourneeId,
                        'keyetape'     => Str::uuid()->toString(),
                        'position'     => $etapeData['position'],
                        'latitude'     => $etapeData['latitude'],
                        'longitude'    => $etapeData['longitude'],
                        'dateposition' => now(),
                        'statut'       => 10,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'etapes'  => $etapesCreees
            ], 201); // Ok
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
