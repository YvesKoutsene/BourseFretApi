<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Client\AuthController;
use App\Http\Controllers\V1\Client\FretController;
use App\Http\Controllers\V1\Client\TourneeController;
use App\Http\Controllers\V1\Transporteur\AuthTransController;
use App\Http\Controllers\V1\Transporteur\EtapeController;
use App\Http\Controllers\V1\Transporteur\FretTransController;
use App\Http\Controllers\V1\Transporteur\TourneeTransController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Pour la version 01 du client
Route::prefix('v1')->group(function () {
    // Route de connexion
    Route::post('/v1/connexion', [AuthController::class, 'connexion']);

    Route::middleware('auth:sanctum')->group(function () {

    // Route de déconnexion
    Route::post('/deconnexion', [AuthController::class, 'deconnexion']);

    // Route de renvoie de la liste des pays
    Route::get('/pays/index', [AuthController::class, 'index']);

    // Route de mise à jour de l'utilisateur
    Route::put('/user/update/{key}', [AuthController::class, 'update']);

    // Route de renvoie de la liste de frets d'un client
    Route::get('/fret/index/{key}', [FretController::class, 'index']);

    // Route de lecture d'un fret
    Route::get('/fret/{key}', [FretController::class, 'show']);

    // Route de renvoie de la liste des tournées des frets d'un client
    Route::get('/tournee/index/{key}', [TourneeController::class, 'index']);

    // Route de lecture d'une tournee
    Route::get('/tournee/{key}', [TourneeController::class, 'show']);

    });

});

// Pour la version 01 du transporteur
Route::prefix('/v1/trans')->group(function () {
    Route::post('/connexion', [AuthTransController::class, 'connexion']);

    Route::middleware('auth:sanctum')->group(function () {
        // Route de déconnexion
        Route::post('/deconnexion', [AuthTransController::class, 'deconnexion']);

        // Route de renvoie des frets attribués 
        Route::get('/frets-attribues/{key}', [FretTransController::class, 'index']);

        // Route de renvoie des détails d'un fret
        Route::get('/frets-attribues/show/{key}', [FretTransController::class, 'show']);

        // Route de renvoie de toutes les tournées d'un fret
        Route::get('/tournees-fret/{key}', [TourneeTransController::class, 'index']);

        // Route de renvoie des tournées en cours d'un fret
        Route::get('/tournees-fret/en-cours/{key}', [TourneeTransController::class, 'index2']);

        // Route de récupération des camions et chauffeurs libre d'un transporteur
        Route::get('/disponibilites/{key}', [TourneeTransController::class, 'getDisponibilitesTransporteur']);

        // Route pour la création de tournées d'un fret
        Route::post('/tournees-fret/store/{key}', [TourneeTransController::class, 'store']);

        // Route pour démarrer une tournée
        Route::post('/tournees-fret/demarrer/{key}', [EtapeController::class, 'demarrerTournee']);

        // Route pour clôturer une tournée
        Route::post('/tournees-fret/cloturer/{key}', [EtapeController::class, 'cloturerTournee']);

        // Route pour renvoyer les étapes d'une tournée
        Route::get('/tournees-fret/etapes/index/{key}', [EtapeController::class, 'index']);

        // Route pour ajouter les étapes des tournées en cours d'un fret
        Route::post('/tournees-fret/etapes/store', [EtapeController::class, 'store']);
    });
   
});
