<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Tests\TestCase;


class UserControllerLogoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('User_Token')->accessToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post(route('user.logout'));

        // Check correct logout (status code 200)
        $response->assertStatus(200);
    }
}
