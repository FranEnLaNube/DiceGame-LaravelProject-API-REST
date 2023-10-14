<?php

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

//--------------Routes without authentication

// Create-register a new user
// TODO inproove consistency in routes, choose "user" or "player"
Route::post('players', [UserController::class, 'register'])->name('players.register');

// Log in an existing user
Route::post('login', [UserController::class, 'login'])->name('user.login');

//---------------Routes with authentication

Route::middleware('auth:api')->group(function () {

    // Log out a logged in user
    Route::post('logout', [UserController::class, 'logout'])->name('user.logout');

    // Get a specific user
    Route::get('players/{id}', [UserController::class, 'show'])->name('player.show');

    // Update an User
    Route::put('players/{id}', [UserController::class, 'update'])->name('player.update');

    // Delete an User
    Route::delete('players/{id}', [UserController::class, 'destroy'])->name('player.destroy');
});
