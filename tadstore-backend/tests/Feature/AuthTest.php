<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class  AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_and_login()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '!paSsWord12345',
            'password_confirmation' => '!paSsWord12345',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure(['user', 'access_token', 'token_type']);

        $login = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => '!paSsWord12345',
        ]);

        $login->assertStatus(200)
                ->assertJsonStructure(['user', 'access_token', 'token_type']);
    }
}
