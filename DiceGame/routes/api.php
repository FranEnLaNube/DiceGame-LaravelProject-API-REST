<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
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
//---------------------- UNAUTHENTICATED ROUTES --------------------

Route::post('players', [UserController::class, 'store'])->name('user.register'); // Create-register a new user
Route::post('login',   [UserController::class, 'login'])->name('user.login'); // Log in an existing user

//---------------------- AUTHENTICATED ROUTES ----------------------

Route::middleware('auth:api')->group(function () {

    Route::middleware('auth:api, scope:player,admin')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('user.logout'); // Log out a logged in user
    });

    Route::middleware('auth:api, scope:player')->group(function () {
        Route::get('players/{id}',           [UserController::class, 'show'])->where('id', '[0-9]+')->name('player.showPlayer'); // Show a specific user // TODO this one is not asked at the project
        Route::put('players/{id}',           [UserController::class, 'update'])->where('id', '[0-9]+')->name('player.updatePlayer'); // Update a User
        Route::delete('players/{id}',        [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('player.destroyPlayer'); // Delete a User // TODO this one is not asked at the project
        Route::post('players/{id}/games',    [GameController::class, 'store'])->name('player.playGame'); // A specific user create a new game by rolling the dice
        Route::get('players/{id}/games',     [GameController::class, 'showPlayerGames'])->name('player.showPlayerGames'); // Get all games of one player
        Route::delete('players/{id}/games',  [GameController::class, 'destroyPlayerGames'])->name('player.destroyPlayerGames'); // A specific user delete their games
    });

    Route::middleware('auth:api, scope:admin')->group(function () {
        Route::get('players',                [GameController::class, 'index'])->name('admin.showPlayers'); // Gets all players in the database with their success percentages
        Route::get('players/ranking',        [GameController::class, 'ranking'])->name('admin.showRanking'); // Get a ranking with success percentages of all players
        Route::get('players/ranking/loser',  [GameController::class, 'showLoser'])->name('admin.showLoser'); // Get the player with the worst success percentage
        Route::get('players/ranking/winner', [GameController::class, 'showWinner'])->name('admin.showWinner'); // Get the player with the best success percentage

    });
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
