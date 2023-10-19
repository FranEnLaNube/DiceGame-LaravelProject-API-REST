<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Laravel\Passport\Passport;

class GameController_ShowLoser extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_get_worst_player()
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

    public function test_admin_cannot_see_worst_player_without_admin_permission()
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
