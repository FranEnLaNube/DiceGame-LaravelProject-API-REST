<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * CREATE A NEW GAME FOR A SPECIFIC USER.
     *
     * @group games
     * @urlParam user_id. The id of the user who is playing. Example: 1
     *
     * @response 201 {
     * "message": "game created successfully",
     *      "data": {
     *      "dice_1": 4,
     *      "dice_2": 3,
     *      "gameWon": "Won",
     *      "user_id": "1",
     *      "updated_at": "2023-10-15T15:09:02.000000Z",
     *      "created_at": "2023-10-15T15:09:02.000000Z",
     *      "id": 1
     *  }
     * }
     * @response 422 {
     *     "message": 'User not found',
     * }
     * @response 422 {
     *     "message": 'Validation failed',
     *     "error": "Different Laravel validation messages from lang/en/validation.php",
     * }
     * @response 401 {
     *     "error": "Unauthorized",
     * }
     */
    public function store(string $user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'],
                422
            );
        }
        $game = new Game();
        $game->dice_1 = $game->rollDice();
        $game->dice_2 = $game->rollDice();
        $input = [
            'dice_1' =>  $game->dice_1,
            'dice_2' =>  $game->dice_2,
            'gameWon' => $game->isGameWon(),
            'user_id' => $user_id,
        ];
        $validationRules = [
            'dice_1' => 'required|min:1|max:6',
            'dice_2' => 'required|min:1|max:6',
            'gameWon' => 'required|in:Won,Lost',
            'user_id' => 'required|numeric|exists:users,id',
        ];

        // Validate request inputs
        $validator = validator($input, $validationRules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(
                ['message' => 'Validation failed', 'errors' => $validator->errors()],
                422
            );
        }
        // Check if user is authenticated with token
        $authUser = Auth::user();
        if ($authUser->id == $user_id) {
            $game = Game::create($input);
            return response()->json(
                ['message' => 'game created successfully', 'data' => $game],
                201
            );
        }
        return response()->json(
            ['error' => 'Unauthorized'],
            401
        );
    }

    /**
     * SHOW ALL GAMES PLAYED BY A SPECIFIC PLAYER.
     *
     * @group games
     * @urlParam user_id The id of the player. Example: 1
     *
     * @response 422 {
     *     "message": "User not found"
     * }
     * @response 200 {
     *     "message": "The player has not played any games yet"
     * }
     * @response 200 {
     *     "message": "Games found",
     *     "data": [
     *         {
     *             "id": 1,
     *             "dice_1": 4,
     *             "dice_2": 3,
     *             "gameWon": "Won",
     *             "user_id": "1",
     *             "created_at": "2023-10-15T15:09:02.000000Z",
     *             "updated_at": "2023-10-15T15:09:02.000000Z"
     *         },
     *         {
     *             "id": 2,
     *             "dice_1": 2,
     *             "dice_2": 5,
     *             "gameWon": "Lost",
     *             "user_id": "1",
     *             "created_at": "2023-10-16T09:21:45.000000Z",
     *             "updated_at": "2023-10-16T09:21:45.000000Z"
     *         }
     *     ]
     * }
     * @response 401 {
     *     "error": "Unauthorized",
     * }
     */
    public function showPlayerGames(string $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(
                ['message' => 'User not found'],
                422
            );
        }
        // Check if user is authenticated with token
        $authUser = Auth::user();
        if ($authUser->id == $user_id) {
            // Using eloquent relationship to bring all games played by a specific user
            $games = $user->games;
            if ($games->isEmpty()) {
                return response()->json(
                    ['message' => 'The player has not played any games yet'],
                    200
                );
            }
            return response()->json(
                ['message' => 'Games found', 'data' => $games],
                200
            );
        }
        return response()->json(
            ['error' => 'Unauthorized'],
            401
        );
    }
    /*     // A specific user delete their games

    // DELETE /players/{id}/games: elimina les tirades del jugador/a.
    Route::delete('players/{id}/games', [GameController::class, 'destroyPlayerGames'])->name('game.playerDeletes');
 */
    /**
     * REMOVE GAMES FROM A SPECIFIC PLAYER.
     *
     * @group games
     * @urlParam user_id. The id of the user who is deleting. Example: 1
     *
     * @response 422 {
     *     "error": "User not found"
     * }
     * @response 200 {
     *      "message": "The player has not played any games yet",
     * }
     * @response 200 {
     *      "message": "The games of the player have been deleted",
     * }
     * @response 401 {
     *     "error": "Unauthorized",
     * }
     */
    public function destroyPlayerGames(string $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(
                ['error' => 'User not found'],
                422
            );
        }
        // Check if user is authenticated with token
        $authUser = Auth::user();
        if ($authUser->id == $user_id) {
            // Using eloquent relationship to bring all games played by this specific user.
            $games = $user->games;
            // Check if players haven't played any games yet or deleted previously.
            if ($games->isEmpty()) {
                return response()->json(
                    ['message' => 'The player has not played any games yet'],
                    200
                );
            }
            // Delete all games of this specific user one by one
            foreach ($games as $game) {
                $game->delete();
            }
            return response()->json(
                ['message' => 'The games of the player have been deleted'],
                200
            );
        }
        return response()->json(
            ['error' => 'Unauthorized'],
            401
        );
    }
}
