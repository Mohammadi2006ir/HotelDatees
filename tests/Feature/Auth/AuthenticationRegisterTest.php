<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationRegisterTest extends TestCase
{
    /** @test **/
    public function it_can_register_a_user_with_valid_data()
    {
        // Act: Attempt to register a user
        $response = $this->postJson('/api/v1/register', [
            'name' => 'ahmadi',
            'email' => 'ahmadi@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        // Assert: Check the response
        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully'])
            ->assertJsonStructure(['user' => ['id', 'name', 'email']]);

        // Assert: Check if the user is created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'ahmadi@gmail.com',
        ]);
    }

    /** @test */
    public function it_cannot_register_a_user_with_duplicate_email()
    {
        // Arrange: Create a user
        User::create([
            'name' => 'ahmadi',
            'email' => 'ahmadi@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // Act: Attempt to register another user with the same email
        $response = $this->postJson('/api/v1/register', [
            'name' => 'ahmadi',
            'email' => 'ahmadi@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        // Assert: Check the response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_cannot_register_a_user_with_short_password()
    {
        // Act: Attempt to register a user with a short password
        $response = $this->postJson('/api/v1/register', [
            'name' => 'ahmadi',
            'email' => 'ahmadi@gmail.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        // Assert: Check the response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_cannot_register_a_user_with_unconfirmed_password()
    {
        // Act: Attempt to register a user with an unconfirmed password
        $response = $this->postJson('/api/register', [
            'name' => 'ahmadi',
            'email' => 'ahmadi@gmail.com',
            'password' => '12345678',
            'password_confirmation' => 'differentpassword',
        ]);

        // Assert: Check the response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
