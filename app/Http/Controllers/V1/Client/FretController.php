<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Fret;

class FretController extends Controller
{
    // Fonction de récupération des frets actifs d'un client
    public function index(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        // Récupération du client
        $client = Client::where('keyclient', $key)->first();

        if (!$client) {
            return response()->json(null, 404); // Client non trouvé
        }

        $query = Fret::where('idclient', $client->id)
            ->whereIn('statut', [30, 40, 50]); 


        // Recherche par numéro fret ou numero dossier
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('numerofret', 'like', "%{$q}%")
                         ->orWhere('numerodossier', 'like', "%{$q}%");
            });
        }

        // Chargement des relations
        $query->with(['lieuchargement', 'lieudechargement', 'typemarchandise','typevehicule']);

        // Pagination
        $pageSize = $request->input('page_size', 15); // Before 15
        $page = $request->input('page', 1); // Before 1

        $frets = $query->paginate($pageSize, ['*'], 'page', $page);

        if ($frets->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json($frets, 200); // Ok
    }


    // Fonction de lecture d'un fret
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
