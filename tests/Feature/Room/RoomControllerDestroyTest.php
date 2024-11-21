<?php

namespace Tests\Feature\Room;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomControllerDestroyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_successful_room_deletion()
    {
        // Arrange
        $user = User::find(1);
        $hotel = Hotel::factory()->create();

        // Act
        $response = $this->actingAs($user)->deleteJson("/api/v1/rooms/5");

        // Assert
        $response->assertStatus(204); // No Content
        $this->assertDatabaseMissing('rooms', [
            'id' => 5,
        ]);
    }
}
