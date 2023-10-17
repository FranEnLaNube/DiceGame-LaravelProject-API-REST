<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerLoginTest extends TestCase
{
    //use RefreshDatabase;
    use DatabaseTransactions;
    public function test_user_can_login()
    {
        // New user with password 'PASSWORD'
        $user = User::factory()->create(['password' => 'PASSWORD']);

        $loginUserData = [
            'email' => $user->email,
            'password' => 'PASSWORD',
        ];
        // Make a POST request to login route
        $response = $this->post(route('user.login'), $loginUserData);

        // Check correct login (status code 200)
        $response->assertStatus(200);
        // Check if gets a correct Json response
        $response->assertJson(['message' => 'User token successfully created']);

        // Check if user is successfully logged in
        $this->assertAuthenticated();
    }
    public function test_user_cannot_login_with_incorrect_password()
    {
        // New user with password 'PASSWORD'
        $user = User::factory()->create(['password' => 'PASSWORD']);

        $loginUserData = [
            'email' => $user->email,
            'password' => 'otherPassword'
        ];
        // Make a POST request to login route
        $response = $this->post(route('user.login'), $loginUserData);

        // Check if gets a correct Json response for incorrect password
        // (403) HTTP status code
        $response->assertJson(['message' => 'Email or password is incorrect, please try again.']);

        // Assert that a user is not authenticated:
        $this->assertGuest();
    }
    public function test_user_cannot_login_with_incorrect_email()
    {
        // New user with password 'PASSWORD'
        User::factory()->create(['password' => 'PASSWORD']);

        $loginUserData = [
            'email' => 'other_email@mail.com',
            'password' => 'PASSWORD',
        ];
        // Make a POST request to login route
        $response = $this->post(route('user.login'), $loginUserData);

        // Check if gets a correct Json response for incorrect (inexistent) email address
        // (403) HTTP status code
        $response->assertJson(['message' => 'Email or password is incorrect, please try again.']);

        // Assert that a user is not authenticated:
        $this->assertGuest();
    }
}
