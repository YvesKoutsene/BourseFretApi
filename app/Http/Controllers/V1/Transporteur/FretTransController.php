<?php

namespace App\Http\Controllers\V1\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transporteur;
use App\Models\Fret;

use App\Models\Propositionprix;
use Illuminate\Support\Str;

class FretTransController extends Controller
{
    // Fonction de récuperation des attributions de frets actifs pour un transporteur
    public function index(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $transporteur = Transporteur::where('keytransporteur', $key)->first();

        if (!$transporteur) {
            return response()->json(null, 404); // Transporteur non trouvé
        }

        // Récupérer les frets attribués au transporteur avec le nombre de tournées
        $frets = $transporteur->fretsAttribues()
            ->withCount('tournees')
            ->get();

        if ($frets->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json([
            'frets' => $frets
        ], 200); // Ok
    }


    // Fonction permettant d'afficher les détails d'un fret
    public function show(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $fret = Fret::with([
            'lieuchargement',
            'lieudechargement',
            'typemarchandise',
            'parametresvehicule',
            'typevehicule',
        ])->where('keyfret', $key)->first();

        if (!$fret) {
            return response()->json(null, 404); // Fret non trouvé
        }
        // 404 même si le user ne met pas le keyfret

        return response()->json([
            'fret' => $fret,
        ], 200); // Ok
    }

    // Fonction pour renvoyer les frets introduits avec leur proposition
    public function getFretIntroduits(Request $request)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $frets = Fret::where('statut', 20)
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

    // Fonction pour renvoyer les propostions de fret
    public function getPropositionsPrix(Request $request, $keyfret)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $fret = Fret::where('keyfret', $keyfret)
            ->with(['propositions' => function ($query) {
                $query->whereIn('statut', [0, 1, 2])
                      ->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$fret) {
            return response()->json(null, 404); // Fret introuvable
        }

        $propositions = $fret->propositions;

        if ($propositions->isEmpty()) {
            return response()->json(null, 204); // Aucune proposition trouvée
        }

        return response()->json([
            'propositions' => $propositions
        ], 200);
    }

    // Fonction pour faire une proposition de prix (On va utiliser idtransporteur)
    public function storePrix(Request $request, $keyfret)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $request->validate([
            'prix' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string|max:255',
            'idtransporteur' => 'required|exists:transporteur,id',
        ]);

        // Trouver le fret
        $fret = Fret::where('keyfret', $keyfret)->first();

        if (!$fret) {
            return response()->json(null, 404); // Fret introuvable
        }

        // Vérifier s’il existe déjà une proposition en cours (0) ou acceptée (1) pour ce fret
        $existe = Propositionprix::where('idfret', $fret->id)
            ->whereIn('statut', [0, 1])
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Ce fret a déjà une proposition en cours ou acceptée.'], 400);
        }

        $user = $request->user(); // ??

        // Créer la proposition
        $proposition = Propositionprix::create([
            'keypropositionprix' => Str::uuid()->toString(),
            'idfret' => $fret->id,
            'idtransporteur' => $request->idtransporteur,
            'prix' => $request->prix,
            'commentaire' => $request->commentaire ?? '',
            'raisonrefus' => '',
            'statut' => 0,
            'createdby' => $user->id ?? null, // ??
        ]);

        return response()->json([
            'proposition' => $proposition
        ], 201);
    }
}
