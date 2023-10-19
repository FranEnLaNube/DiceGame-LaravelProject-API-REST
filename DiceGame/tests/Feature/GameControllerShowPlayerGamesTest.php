<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GameControllerShowPlayerGamesTest extends TestCase
{
    use DatabaseTransactions;
    public function test_user_cannot_see_games_for_nonexistent_player()
    {
        // New "player" user
        $user = Passport::actingAs(User::factory()->create(), ['player']);

        // Pretend to fetch games for a nonexistent player
        $response = $this->get(route('player.showPlayerGames', ['id' => -999]));

        // Check for a 422 status code
        $response->assertStatus(422);
    }
    public function test_user_cannot_see_games_without_player_permission()
    {
        // Create a new user but don't give it the player permission
        $user = User::factory()->create();

        // Try to fetch games without the "player" permission
        $response = $this->get(route('player.showPlayerGames', ['id' =>  $user->id]));

        // Check for a 302 status code, redirection
        $response->assertStatus(302);
    }
    public function test_user_cannot_see_games_for_another_player()
    {
        // Create and authenticate a new users players
        $user1 = Passport::actingAs(User::factory()->create(), ['player']);
        // Give authentication to another user
        $user2 = Passport::actingAs(User::factory()->create(), ['player']);
        //$user_id = $user1->id;

        // Try to fetch games for a different player
        $response = $this->get(route('player.showPlayerGames', ['id' => $user1->id]));

        // Check for a 403 status code
        $response->assertStatus(403);
    }
    public function test_user_can_see_games_for_existing_player()
{
    // New "player" user
    $user = Passport::actingAs(User::factory()->create(), ['player']);

    // Create a game for that user
    Game::create([
        'dice_1' => 4,
        'dice_2' => 3,
        'gameWon' => 'Won',
        'user_id' => $user->id,
    ]);

    // Try to get that game
    $response = $this->get(route('player.showPlayerGames', ['id' =>  $user->id]));

    // Check for a 200 status code
    $response->assertStatus(200);

    // Check JSON response structure
    $response->assertJsonStructure([
        'message',
        'Games' => [
            '*' => [
                'id',
                'dice_1',
                'dice_2',
                'gameWon',
                'user_id',
                'created_at',
                'updated_at',
            ],
        ],
        'User success rate',
    ]);
}

}
