<?php

namespace Tests\Feature\Hotel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateHotelTest extends TestCase
{
    public function test_it_can_create_a_hotel(): void
    {
        $data = [
            'name' => 'Persian',
            'city' => 'Tehran',
            'star_rating' => 4,
        ];

        $response = $this->postJson('/api/v1/hotels', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('hotels', $data);
    }

    /** @test */
    public function it_requires_name_to_create_a_hotel()
    {
        $data = [
            'city' => 'Tehran',
            'star_rating' => 4,
        ];

        $response = $this->postJson('/api/v1/hotels', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_requires_city_to_create_a_hotel()
    {
        $data = [
            'name' => 'Persian',
            'star_rating' => 4,
        ];

        $response = $this->postJson('/api/v1/hotels', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('city');
    }

    /** @test */
    public function it_requires_star_rating_to_create_a_hotel()
    {
        $data = [
            'name' => 'Persian',
            'city' => 'Tehran',
        ];

        $response = $this->postJson('/api/v1/hotels', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('star_rating');
    }
}
