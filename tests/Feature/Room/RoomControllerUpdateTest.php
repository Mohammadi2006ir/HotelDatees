<?php

namespace Tests\Feature\Room;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomControllerUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_not_authorized_to_update_room()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'room_type' => 'Deluxe',
            'capacity' => 4,
            'price' => 150.00,
            'status' => 'available',
        ]);

        // Act
        $response = $this->actingAs($user2)->putJson("/api/v1/rooms/{$room->id}", [
            'hotel_id' => $hotel->id,
            'room_type' => 'Super Deluxe',
            'capacity' => 5,
            'price' => 200.00,
            'status' => 'available',
        ]);

        // Assert
        $response->assertStatus(403) // فرض بر این است که کاربر مجاز به ویرایش نیست
        ->assertJson(['message' => 'شما مجاز به ویرایش این اتاق نیستید.']);
    }

    public function test_successful_room_update()
    {
        // Arrange
        $user = User::find(1);
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'room_type' => 'Deluxe',
            'capacity' => 4,
            'price' => 150.00,
            'status' => 'available',
        ]);

        // Act
        $response = $this->actingAs($user)->putJson("/api/v1/rooms/{$room->id}", [
            'hotel_id' => $hotel->id,
            'room_type' => 'Super Deluxe',
            'capacity' => 5,
            'price' => 200.00,
            'status' => 'available',
        ]);

        // Assert
        $response->assertStatus(200); // OK
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'room_type' => 'Super Deluxe',
            'capacity' => 5,
            'price' => 200.00,
            'status' => 'available',
        ]);
    }
}
