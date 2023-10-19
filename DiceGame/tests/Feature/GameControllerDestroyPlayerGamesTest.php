<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Laravel\Passport\Passport;

class GameControllerDestroyPlayerGamesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_destroy_games_of_specific_player()
    {
        // Create a "player" user and give them the "player" permission
        $user = Passport::actingAs(User::factory()->create(), ['player']);

        // Create a game for that user
        Game::create([
            'dice_1' => 4,
            'dice_2' => 3,
            'gameWon' => 'Won',
            'user_id' => $user->id,
        ]);
        // Try to delete the games of the player
        $response = $this->delete(route('player.destroyPlayerGames', ['id' => $user->id]));

        // Check for a 200 status code
        $response->assertStatus(200);

        // Check if games have been deleted
        $this->assertEmpty(Game::where('id', $user->id)->get());
    }
    public function test_user_cannot_destroy_games_of_another_player()
{
    // Create and authenticate a "player" user
    $user1 = Passport::actingAs(User::factory()->create(), ['player']);
    // Give authentication to another "player" user
    $user2 = Passport::actingAs(User::factory()->create(), ['player']);

    // Create a game for that user
    Game::create([
        'dice_1' => 4,
        'dice_2' => 3,
        'gameWon' => 'Won',
        'user_id' => $user1->id,
    ]);
    // Try to delete the games of the first player using the second player's token
    $response = $this->delete(route('player.destroyPlayerGames', ['id' => $user1->id]));

    // Check for a 403 status code
    $response->assertStatus(403);

    // Check if game has not been deleted (first game is id 1)
    $this->assertNotEmpty(Game::where('id', 1)->get());
}

}
