<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Laravel\Passport\Passport;

class GameControllerPlayGameTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_create_game_for_specific_user()
    {
        // New "player" user
        $user = Passport::actingAs(User::factory()->create(), ['player']);

        $user_id = $user->id;

        // POST request to create a game
        $response = $this->post(route('player.playGame', ['id' => $user_id]));
        $response->assertStatus(201);

        // Check JSON response structure
        $response->assertJsonStructure([
            'message',
            'data' => [
                'dice_1',
                'dice_2',
                'gameWon',
                'user_id',
                'updated_at',
                'created_at',
                'id',
            ],
            'User success rate',
        ]);
    }

    public function test_user_cannot_create_game_for_nonexistent_user()
    {
        // New "player" user
        Passport::actingAs(User::factory()->create(), ['player']);

        // pretend to create a new game for an invalid user
        $response = $this->post(route('player.playGame', ['id' => -999]));

        // Check 422 status code
        $response->assertStatus(422);
    }

    public function test_user_cannot_create_game_if_not_authenticated()
    {
        // Create a new user but don't give a permission
        $user = User::factory()->create();
        $user_id = $user->id;

        // Try to create a new game
        $response = $this->post(route('player.playGame', ['id' => $user_id]));

        // Check for redirection
        $response->assertStatus(302);
    }

    public function test_user_cannot_create_game_for_another_user()
    {
        // Create and authenticate a new users players
        $user1 = Passport::actingAs(User::factory()->create(), ['player']);
        // Give authentication to another user
        $user2 = Passport::actingAs(User::factory()->create(), ['player']);
        $user_id = $user1->id;

        // Try to create a new game
        $response = $this->post(route('player.playGame', ['id' => $user_id]));

        // Check for 403 status code
        $response->assertStatus(403);
    }
}
