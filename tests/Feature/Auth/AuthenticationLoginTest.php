<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationLoginTest extends TestCase
{
    /** @test */
    public function it_can_login_with_valid_credentials(): void
    {
        // Act: Attempt to log in
        $response = $this->postJson('/api/v1/login', [
            'email' => 'ahmadi@gmail.com',
            'password' => '12345678',
        ]);

        // Assert: Check the response
        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user' => ['id', 'email', 'roles']]);
    }

    /** @test */
    public function it_cannot_login_with_invalid_email()
    {
        // Act: Attempt to log in with an invalid email
        $response = $this->postJson('/api/v1/login', [
            'email' => 'invalid@example.com',
            'password' => '12345678',
        ]);

        // Assert: Check the response
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    public function it_cannot_login_with_invalid_password()
    {
        // Act: Attempt to log in with an invalid password
        $response = $this->postJson('/api/v1/login', [
            'email' => 'ahmadi@gmail.com',
            'password' => 'wrongpassword',
        ]);

        // Assert: Check the response
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }
}
