<?php

namespace Tests\Feature\Reservation;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationControllerDestroyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_not_authorized_to_delete_reservation()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user1->id,
            'room_id' => $room->id,
        ]);

        // Act
        $response = $this->actingAs($user2)->deleteJson("/api/v1/reserves/{$reservation->id}");

        // Assert
        $response->assertStatus(403)
            ->assertJson(['message' => 'شما مجاز به حذف این رزرو نیستید.']);
    }

    public function test_successful_reservation_deletion()
    {
        // Arrange
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        // Act
        $response = $this->actingAs($user)->deleteJson("/api/v1/reserves/{$reservation->id}");

        // Assert
        $response->assertStatus(204); // No Content
        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }
}
