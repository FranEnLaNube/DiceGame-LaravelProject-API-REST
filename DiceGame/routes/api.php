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
// TODO group routes with players/ as a prefix

// Create a new user
Route::post('players', [UserController::class, 'store'])->name('players.register');

// Get a specific user
Route::get('players/{id}', [UserController::class, 'show'])->name('player.show');// TODO: Is it ok this "playerS"?

// Update an User
Route::put('players/{id}', [UserController::class, 'update'])->name('player.update');

// Delete an User
Route::delete('players/{id}', [UserController::class, 'destroy'])->name('player.destroy');

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */
