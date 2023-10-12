<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControlerUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_with_valid_data()
    {
        // Create a new user at testing database
        $user = User::factory()->create();
        // Authenticate $user if route is protected
        // TODO uncomment this when protect routes
        //$this->actingAs($user);
        $newUserData = [
            'nickname' => 'NewNickname',
        ];
        // Make a PUT request to update user route with new data
        $response = $this->put(route('player.update', ['id' => $user->id]), $newUserData);
        // TODO Check if is correct a 200 or a 201 response
        $response->assertStatus(200); // Check if gets a HTTP 201 response (registered succesfully).
        // Check if the user is updated in database
        $this->assertDatabaseHas('users', ['nickname' => 'NewNickname']);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        // Create two new users at testing database
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        // Authenticate $user if route is protected
        // TODO uncomment this when protect routes
        //$this->actingAs($user1);
        //$this->actingAs($user2);

        // Intenta actualizar el usuario 2 con el 'nickname' del usuario 1
        // Try to update User2 with User1's nickname
        $response = $this->put(route('player.update', ['id' => $user2->id]), ['nickname' => $user1->nickname]);

        // Check that the response is a JSON (status code 422)
        $response->assertStatus(422);

        // Check errors in the JSON response
        $response->assertJsonValidationErrors('nickname');
    }
    public function test_user_not_found()
    {
        // Create a new user at testing database
        $user = User::factory()->create();
        $newUserData = [
            'nickname' => 'NewNickname',
        ];
        // Make a PUT request to update user route with new data
        $response = $this->put(route('player.update', ['id' => ($user->id + 1)]), $newUserData);
        $response->assertStatus(404); // Check if gets a HTTP 404 response (user not found).
    }
}