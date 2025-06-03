<?php

namespace App\Http\Controllers\V1\Affreteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fret;
use App\Models\Propositionprix;
use Illuminate\Support\Str;

class PropxController extends Controller
{
    // Fonction pour renvoyer les propostions de fret
    public function index(Request $request, $keyfret)
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
    public function store(Request $request, $keyfret)
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
