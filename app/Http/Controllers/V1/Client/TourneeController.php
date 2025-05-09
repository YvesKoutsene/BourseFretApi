<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournee;
use App\Models\Fret;
use App\Models\Client;

class TourneeController extends Controller
{
    // Fonction permettant de ramener les tournées d'un fret
    public function index(Request $request, $key)
    {
        // Vérification de l'authentification 
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        // Récupérer le fret en utilisant le keyfret
        $fret = Fret::with(['lieuchargement', 'lieudechargement'])
            ->where('keyfret', $key)
            ->first();

        if (!$fret) {
            return response()->json(null, 404); // Fret non trouvé
        }

        $pageSize = $request->input('page_size', 10);
        $search = $request->input('q');

        // Récupérer toutes les tournées associées au fret
        $tournees = Tournee::with(['fret','derniereEtape', 'camionActif'])
            ->where('idfret', $fret->id)
            ->orderByDesc('created_at')->get();

        if ($search) {
            $tournees = $tournees->where(function ($query) use ($search) {
                $query->where('numerobl', 'like', "%$search%")
                    ->orWhere('numeroconteneur', 'like', "%$search%"); 
            });
        }

        $tournees = $tournees->paginate($pageSize);

        if ($tournees->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json($tournees, 200); // Ok
    }


    // Fonction de lecture d'une tournee
    public function show(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $tournee = Tournee::with(['fret', 'camionActif'])
        ->where('keytournee', $key)
        ->first();

        if (!$tournee) {
            return response()->json(null, 404);
        }

        $tournee->etapes = $tournee->etapes()->orderByDesc('dateposition')->get();

        return response()->json($tournee, 200); // Ok
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
