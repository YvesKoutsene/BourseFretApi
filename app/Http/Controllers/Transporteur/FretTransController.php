<?php

namespace App\Http\Controllers\Transporteur;

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

        // Puis on récupère ses frets attribués actifs
        $frets = $transporteur->fretsAttribues()->get();

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


}
