<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
// TODO group routes with players/ as a prefix or as it´s be needed

//----------- GUEST routes - without authentication

//---------------- UserController routes

// Create-register a new user
Route::post('players', [UserController::class, 'store'])->name('user.register');

// Log in an existing user
Route::post('login', [UserController::class, 'login'])->name('user.login');

//----------- USER-PLAYER-ADMIN routes - with authentication

Route::middleware('auth:api')->group(function () {

    //---------------- UserController routes - with authentication

    // Log out a logged in user
    Route::post('logout', [UserController::class, 'logout'])->name('user.logout'); // TODO inproove consistency in routes, choose "user" or "player"

    // Get a specific user
    Route::get('players/{id}', [UserController::class, 'show'])->where('id', '[0-9]+')->name('player.show'); // TODO inproove consistency in routes, choose "user" or "player"

    // Update an User
    Route::put('players/{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->name('player.update'); // TODO inproove consistency in routes, choose "user" or "player"

    // Delete an User
    Route::delete('players/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('player.destroy'); // TODO This one could be deleted because player is not authorized to delete himself and is not asked at the project // TODO inproove consistency in routes, choose "user" or "player"

    //----------- USER-PLAYER-ADMIN routes - with authentication

    //---------------- GameController routes - with authentication

    // A specific user create a new game by rolling the dice

    // POST /players/{id}/games/ : un jugador/a específic realitza una tirada dels daus.
    Route::post('players/{id}/games', [GameController::class, 'store'])->name('game.playerPlays');

    // Get all games of one player

    // GET /players/{id}/games: retorna el llistat de jugades per un jugador/a.
    Route::get('players/{id}/games', [GameController::class, 'showPlayerGames'])->name('game.showGames');

    // A specific user delete their games

    // DELETE /players/{id}/games: elimina les tirades del jugador/a.
    Route::delete('players/{id}/games', [GameController::class, 'destroyPlayerGames'])->name('game.playerDeletes');

    //----------- USER-ADMIN routes - with authentication

    //---------------- UserController routes - with authentication

    // Get a ranking with success percentages of all players

    // GET /players/ranking: retorna el rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
    Route::get('players/ranking', [GameController::class, 'ranking'])->name('admin.showRanking');

    // Get the player withn the worst success percentage

    // GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
    Route::get('players/ranking/loser', [GameController::class, 'showLoser'])->name('admin.showLoser');

    // Get the player with the best success percentage

    // GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.
    Route::get('players/ranking/winner', [GameController::class, 'showWinner'])->name('admin.showWinner');

    // Gets all players in the database with their success percentages

    // GET /players: retorna el llistat de tots els jugadors/es del sistema amb el seu percentatge mitjà d’èxits
    Route::get('players', [GameController::class, 'index'])->name('admin.indexPlayers');
});

// If token is not authorized, invalid, expired or incorrect
Route::get('/', function () {
    $response = [
        'success' => false,
        'message' => 'Invalid credentials. Please log in again.',
    ];

    // send 'unauthorized' response back to client
    return response()->json($response, 401);
})->name('login');
