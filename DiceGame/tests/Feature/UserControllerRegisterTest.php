<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerRegisterTest extends TestCase
{
    use DatabaseTransactions;
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post(route('user.register'), [ // It's the same to use $this->post('api/players'...
            'nickname' => 'testUser',
            'email' => 'testuser@example.com',
            'password' => 'secret_password',
            'password_confirmation' => 'secret_password',
        ]);

        $response->assertStatus(201); // Check if gets a HTTP 201 response (registered).
        //TODO check need of this line:
        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']); // Checks if users is at database.
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $response = $this->post(route('user.register'), [
            'nickname' => 'testUser',
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Too short password
            'password_confirmation' => 'different_password', // Different password
        ]);

        $response->assertStatus(422); // Check if gets a HTTP 422 response (Invalid request).
        // TODO check need of this line:
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']); // Checks if users is not created in database.
    }
}
