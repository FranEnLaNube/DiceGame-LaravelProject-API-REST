<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class GameController_ShowWinnerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_get_best_player()
    {
        // Create a "admin" user and give them the "admin" permission
        Passport::actingAs(User::factory()->create(), ['admin']);

        // Fetch the best player
        $response = $this->get(route('admin.showWinner'));

        // Check for a 200 status code
        $response->assertStatus(200);

        // Check JSON response structure
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_admin_cannot_see_best_player_without_admin_permission()
    {
        // Create a "player" user and give them the "player" permission
        Passport::actingAs(User::factory()->create(), ['player']);

        // Fetch the best player
        $response = $this->get(route('admin.showWinner'));

        // Check for a 403 status code
        $response->assertStatus(403);

        // Check JSON response structure
        $response->assertJsonStructure([
            'error',
        ]);
    }
}
