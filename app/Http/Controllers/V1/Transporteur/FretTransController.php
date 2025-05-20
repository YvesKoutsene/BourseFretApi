<?php

namespace App\Http\Controllers\V1\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transporteur;
use App\Models\Fret;

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

    // Pour renvoyer les frets introduits avec leur proposition
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

    // Pour renvoyer les propostions de fret
    public function getPropositionsPrix($keyfret)
    {
        if (!auth()->check()) {
            return response()->json(null, 401);
        }

        $fret = Fret::where('keyfret', $keyfret)
            ->with(['propositions' => function ($query) {
                $query->whereIn('statut', [0, 1, 2]);
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
}
