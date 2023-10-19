<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControlerUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_update_with_valid_data()
    {
        // Create a new user player with Factory
        $user = User::factory()->create();
        // Authenticate $user if route is protected
        Passport::actingAs($user, ['player']);
        $newUserData = [
            'nickname' => 'NewNickname',
        ];
        // Make a PUT request to update user route with new data
        $response = $this->put(route('player.updatePlayer', ['id' => $user->id]), $newUserData);
        // Check if gets a HTTP 200 response (registered succesfully).
        $response->assertStatus(200);
        // Check if the user is updated in database
        $this->assertDatabaseHas('users', ['nickname' => 'NewNickname']);
    }

    public function test_user_cannot_update_with_invalid_data()
    {
        // Create two new users players
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        // Authenticate $user
        Passport::actingAs($user2, ['player']);

        // Try to update User2 with User1's nickname
        $response = $this->put(route('player.updatePlayer', ['id' => $user2->id]), ['nickname' => $user1->nickname]);

        // Check that the response is a JSON (status code 422)
        $response->assertStatus(422);

        // Check errors in the JSON response
        $response->assertJsonValidationErrors('nickname');
    }
    public function test_user_not_found()
    {
        // Create a new user at testing database
        $user = User::factory()->create();
        Passport::actingAs($user);
        $newUserData = [
            'nickname' => 'NewNickname',
        ];
        // Make a PUT request to update user route with new data
        $response = $this->put(route('player.updatePlayer', ['id' => ($user->id * -1)]), $newUserData);
        $response->assertStatus(404); // Check if gets a HTTP 404 response (user not found).
    }
    public function test_user_cannot_update_without_authentication()
    {
        // Create a new user player
        $user = User::factory()->create();

        // Remove authentication

        $newUserData = [
            'nickname' => 'NewNickname',
        ];

        // Make a PUT request to updatePlayer route with new data
        $response = $this->put(route('player.updatePlayer', ['id' => $user->id]), $newUserData);

        // Check if gets a HTTP 302 response (redirect)
        $response->assertStatus(302);
    }
    public function test_user_cannot_update_without_player_permission()
    {
        // Create a new user
        $user = User::factory()->create();
        // Authenticate $user and give Admin permission
        Passport::actingAs($user, ['admin']);

        $newUserData = [
            'nickname' => 'NewNickname',
        ];
        // Make a PUT request to update user route with new data
        $response = $this->put(route('player.updatePlayer', ['id' => $user->id]), $newUserData);

        // Check that the response is a JSON (status code 422)
        $response->assertStatus(403);
    }
}
