<?php

namespace Tests\Feature\Room;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomControllerCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_successful_room_creation()
    {
        // Arrange
        $user = User::find(1);
        $this->actingAs($user); // فرض بر این است که کاربر باید احراز هویت شده باشد

        $roomData = [
            'hotel_id' => 1, // شناسه هتل
            'room_type' => 'Deluxe', // نوع اتاق
            'capacity' => 4, // ظرفیت اتاق
            'price' => 150.00, // قیمت اتاق
            'status' => 'available', // وضعیت اتاق
        ];

        // Act
        $response = $this->postJson('/api/v1/rooms', $roomData);

        // Assert
        $response->assertStatus(201); // Created
        $this->assertDatabaseHas('rooms', [
            'hotel_id' => 1,
            'room_type' => 'Deluxe',
            'capacity' => 4,
            'price' => 150.00,
            'status' => 'available',
        ]);
    }

    public function test_unauthorized_user_cannot_create_room()
    {
        // Arrange
        $user = User::find(2); // کاربر بدون مجوز
        $this->actingAs($user); // احراز هویت کاربر

        $roomData = [
            'hotel_id' => 1,
            'room_type' => 'Deluxe',
            'capacity' => 4,
            'price' => 150.00,
            'status' => 'available',
        ];

        // Act
        $response = $this->postJson('/api/v1/rooms', $roomData);

        // Assert
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
