<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Laravel\Passport\Passport;

use Tests\TestCase;

class GameControllerIndexTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_access_players_list()
    {
        // Create a new admin user
        Passport::actingAs(User::factory()->create(), ['admin']);

        // Create some player users
        //User::factory()->count(2)->create();

        $response = $this->get(route('admin.showPlayers'));

        // Check for correct response (status code 200)
        $response->assertStatus(200);

        // Check JSON output
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'nickname',
                    'successRate',
                ],
            ],
            'Average success rate',
        ]);
    }

    public function test_player_cannot_access_players_list()
    {
        // Create a new non-admin user
        $user = User::factory()->create();
        Passport::actingAs($user, []);

        // Make a GET request to the index route
        $response = $this->get(route('admin.showPlayers'));

        // Check if the response is forbidden (status code 403)
        $response->assertStatus(403);
    }
}
