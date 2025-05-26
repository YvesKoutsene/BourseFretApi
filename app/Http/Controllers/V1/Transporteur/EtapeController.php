<?php

namespace App\Http\Controllers\V1\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournee;
use App\Models\Etape;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EtapeController extends Controller
{
    // Fonction permettant de démarrer une tournée
    public function demarrerTournee(Request $request, $key)
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

        if ($tournee->statut !== 10) {
            return response()->json(['message' => 'Elle est déjà en cours ou clôturée.'], 400);
        }

        // Démarrer la transaction
        DB::beginTransaction();

        try {
            // Création de la première étape
            Etape::create([
                'keyetape'     => Str::uuid()->toString(),
                'position'     => $validated['position'],
                'dateposition' => now(),
                'latitude'     => $validated['latitude'],
                'longitude'    => $validated['longitude'],
                'statut'       => 10,
                'idtournee'    => $tournee->id,
            ]);

            $tournee->statut = 20; // Statut "en cours"
            $tournee->save();

            // Mise en cours du fret associé, **uniquement si ce n'est pas déjà fait**
            if ($fret = $tournee->fret) {
                if ($fret->statut !== 40) {
                    $fret->statut = 40; // Fret en cours
                    $fret->save();
                }
            }

            // Valider la transaction
            DB::commit();

            return response()->json(['tournee' => $tournee], 200); // Ok
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors du démarrage de la tournée: ' . $e->getMessage()], 500);
        }
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

        $tournee = Tournee::with('fret')->where('keytournee', $key)->first();

        if (!$tournee) {
            return response()->json(null, 404); // Tournée non trouvée
        }

        if ($tournee->statut !== 20) {
            return response()->json(['message' => 'Cette tournée est déjà clôturée ou n’est pas en cours.'], 400);
        }

        // Démarrer la transaction
        DB::beginTransaction();

        try {
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

            // Mise à jour du statut de la tournée
            $tournee->statut = 30; // Clôturée
            $tournee->save();

            // Libération du camion
            $camion = $tournee->camionActif()->first();
            if ($camion) {
                $camion->statut = 10; // Disponible
                $camion->save();
            }

            // Libération du chauffeur
            $chauffeur = $tournee->chauffeurActif()->first();
            if ($chauffeur) {
                $chauffeur->statut = 10; // Disponible
                $chauffeur->save();
            }

            // Vérification du fret pour le livrer
            $fret = $tournee->fret;
            if ($fret) {
                $nombreTourneesAttendu = $fret->nombrecamions;
                $nombreTourneesReelles = $fret->tournees()->count();
                $nombreTourneesCloturees = $fret->tournees()->where('statut', 30)->count();

                if ($nombreTourneesReelles === $nombreTourneesAttendu && $nombreTourneesCloturees === $nombreTourneesAttendu) {
                    $fret->statut = 50; // Livré
                    $fret->save();
                }
            }

            // Valider la transaction
            DB::commit();

            return response()->json(['tournee' => $tournee], 200);
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la clôture de la tournée: ' . $e->getMessage()], 500);
        }
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
