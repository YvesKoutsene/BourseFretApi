<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournee;
use App\Models\Fret;
use App\Models\Client;

class TourneeController extends Controller
{
    // Fonction permettant de ramener les tournées des frets d'un client
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

        $pageSize = $request->input('page_size', 10);
        $search = $request->input('q');

        // Récupère les frets du client
        $frets = Fret::whereHas('client', function ($query) use ($key) {
            $query->where('keyclient', $key);
        });

        if ($search) {
            $frets = $frets->where(function ($query) use ($search) {
                $query->where('numerofret', 'like', "%$search%")
                      ->orWhere('numerodossier', 'like', "%$search%");
            });
        }

        // IDs des frets du client (après filtre éventuel)
        $fretIds = $frets->pluck('id');

        // Récupère les tournées liées à ces frets
        $tournees = Tournee::with(['fret', 'camionActif', 'derniereEtape'])
            ->whereIn('idfret', $fretIds)
            ->whereNotIn('statut', [0, 9])
            ->paginate($pageSize);

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

        $tournee = Tournee::with(['fret', 'camionActif', 'etapes'])
            ->where('keytournee', $key)
            ->first();

        if (!$tournee) {
            return response()->json(null, 404); // Tournee pas trouvée
        }
        // 404 même si le user ne met pas le keytournee

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
