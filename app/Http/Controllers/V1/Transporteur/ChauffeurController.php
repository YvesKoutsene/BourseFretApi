<?php

namespace App\Http\Controllers\V1\Transporteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transporteur;
use App\Models\Chauffeur;

class ChauffeurController extends Controller
{
    // Fonction pour renvoyer tous les chauffeur d'un transporteur
    public function index(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $transporteur = Transporteur::where('keytransporteur', $key)->first();

        if (!$transporteur) {
            return response()->json(null, 404); // Transporteur non trouvé
        }

        $chauffeurs = Chauffeur::where('idtransporteur', $transporteur->id)->get();

        if ($chauffeurs->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json([
            'chauffeurs' => $chauffeurs,
        ], 200); // Ok
    }
}
