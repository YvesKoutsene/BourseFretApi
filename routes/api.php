<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\FretController;
use App\Http\Controllers\Client\TourneeController;
use App\Http\Controllers\Transporteur\AuthTransController;
use App\Http\Controllers\Transporteur\EtapeTransController;
use App\Http\Controllers\Transporteur\FretTransController;
use App\Http\Controllers\Transporteur\TourneeTransController;

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

#Pour Client
// Route de connexion
Route::post('/v1/connexion', [AuthController::class, 'connexion']);

// Route de déconnexion
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/deconnexion', [AuthController::class, 'deconnexion']);
});

// Route de mise à jour de l'utilisateur
Route::put('/v1/user/update/{key}', [AuthController::class, 'update']);

// Route de renvoie de la liste de frets d'un client
Route::get('/v1/fret/index/{key}', [FretController::class, 'index']);

// Route de lecture d'un fret
Route::get('/v1/fret/{key}', [FretController::class, 'show']);

// Route de renvoie de la liste des tournées des frets d'un client
Route::get('/v1/tournee/index/{key}', [TourneeController::class, 'index']);

// Route de lecture d'une tournee
Route::get('/v1/tournee/{key}', [TourneeController::class, 'show']);

#En commun pour client et transporteur
// Route de renvoie de la liste des pays
Route::get('/v1/pays/index', [AuthController::class, 'index']);


#Pour Transporteur
// Route de connexion
Route::post('/v1/trans/connexion', [AuthTransController::class, 'connexion']);

// Route de déconnexion
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/trans/deconnexion', [AuthTransController::class, 'deconnexion']);
});

// Route de renvoie des frets attribués 
Route::get('/v1/trans/frets-attribues/{key}', [FretTransController::class, 'index']);

// Route de renvoie de toutes les tournées d'un fret
Route::get('/v1/trans/tournees-fret/{key}', [TourneeTransController::class, 'index']);

// Route de renvoie des tournées en cours d'un fret
Route::get('/v1/trans/tournees-fret/en-cours/{key}', [TourneeTransController::class, 'index2']);

// Route de récupération des camions et chauffeurs libre d'un transporteur
Route::get('/v1/trans/disponibilites/{key}', [TourneeTransController::class, 'getDisponibilitesTransporteur']);

// Route pour la création de tournées d'un fret
Route::post('/v1/trans/tournees-fret/store/{key}', [TourneeTransController::class, 'store']);

// Route pour démarrer une tournée
Route::post('/v1/trans/tournees-fret/demarrer/{key}', [EtapeTransController::class, 'demarrerTournee']); 

// Route pour clôturer une tournée
Route::post('/v1/trans/tournees-fret/cloturer/{key}', [EtapeTransController::class, 'cloturerTournee']); 

// Route pour renvoyer les étapes d'une tournée
Route::get('/v1/trans/tournees-fret/etapes/{key}', [EtapeTransController::class, 'index']); 

// Route pour ajouter les étapes des tournées en cours d'un fret
Route::post('/v1/trans/tournees-fret/etapes/store', [EtapeTransController::class, 'store']); 