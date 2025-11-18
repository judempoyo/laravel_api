<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_endpoint_returns_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/v1/auth/user');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'email' => $user->email,
        ]);
    }
}