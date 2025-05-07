<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Client;
use App\Models\Pays;

class AuthController extends Controller
{
    /**
     * Connexion d’un utilisateur client
     */

    public function connexion(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motdepasse' => 'required|string',
        ]);

        // Chargement de l'utilisateur avec ses relations
        $user = Utilisateur::with(['indicatif', 'pays', 'client'])
            ->where('email', $request->email)
            ->first();

        // Vérification de l'email et du mot de passe
        if (!$user || !Hash::check($request->motdepasse, $user->motdepasse)) {
            return response()->json(null, 401); // Identifiants invalides
        }

        // Vérification du statut actif
        if ($user->statut !== 10) {
            return response()->json(null, 403); // Utilisateur désactivé
        }

        // Restriction : uniquement les clients
        if (!$user->client) {
            return response()->json([
                'message' => 'Seuls les clients peuvent se connecter ici.'
            ], 403);
        }

        // Création du token avec Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user->makeHidden([
                'motdepasse',
                'access_token',
                'createdby',
                'updatedby'
            ]),
        ], 200); // Ok
    }


    /**
     * Déconnexion d’un utilisateur client
     */
    public function deconnexion(Request $request)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $user = Auth::user();

        // Restriction : uniquement les clients
        if (!$user->client) {
            return response()->json([
                'message' => 'Seuls les clients peuvent se deconnecter ici.'
            ], 403);
        }

        // Suppression du token de l'utilisateur
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204); //Ok
    }

    /**
     * Liste de tous les pays
     */
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        $pays = Pays::all();

        if ($pays->isEmpty()) {
            return response()->json(null, 204); // No content
        }

        return response()->json($pays, 200); // Ok
    }

    /**
     * Mise à jour des informations d’un utilisateur client
     */
    public function update(Request $request, $key)
    {
        if (!$request->user()) {
            return response()->json(null, 401); // Non authentifié
        }

        // Récupération de l'utilisateur
        $user = Utilisateur::where('keyutilisateur', $key)->first();
        if (!$user) {
            return response()->json(null, 404); // Utilisateur non trouvé
        }

        $user = Auth::user();

        // Vérifie que le keyutilisateur dans l'URL correspond à celui de l'utilisateur connecté
        if ($user->keyutilisateur !== $key) {
            return response()->json(null, 403); // Accès interdit
        }

        // Restriction : uniquement les clients
        if (!$user->client) {
            return response()->json([
                'message' => 'Seuls les clients peuvent modifier leur compte.'
            ], 403);
        }

        $request->validate([
            'nom'         => 'required|string|max:255',
            'prenom'      => 'required|string|max:255',
            'telephone'   => 'required|string|max:20',
            'indicatif' => 'required|exists:pays,id',
        ]);

        $user->update([
            'nom'         => $request->nom,
            'prenom'      => $request->prenom,
            'telephone'   => $request->telephone,
            'idindicatif' => $request->indicatif,
        ]);

        // Mise à jour dans la table client si nécessaire
        if (is_null($user->createdby) && $user->idclient) {
            $client = $user->client;
            if ($client) {
                $client->update([
                    'nom'     => $request->nom,
                    'prenom'  => $request->prenom,
                    'contact' => $request->telephone,
                ]);
            }
        }

        $user->load(['indicatif', 'pays', 'client']);

        return response()->json([
            'user' => $user->makeHidden(['motdepasse', 'access_token', 'createdby', 'updatedby']),
        ], 200);
    }
}
