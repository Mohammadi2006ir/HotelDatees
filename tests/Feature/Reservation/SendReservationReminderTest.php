<?php

namespace Tests\Feature\Reservation;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendReservationReminderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @test
     */
    public function test_reservation_already_exists()
    {
        // Arrange
        $user = User::factory()->create();
        $room = Room::factory()->create();
        Reservation::factory()->create([
            'room_id' => $room->id,
            'check_in_date' => '2023-10-01',
            'check_out_date' => '2023-10-05',
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/reserves', [
            'room_id' => $room->id,
            'check_in_date' => '2023-10-03',
            'check_out_date' => '2023-10-06',
            'family_members' => 2,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson(['message' => 'Reservation already exists.']);
    }

    public function test_room_capacity_exceeded()
    {
        // Arrange
        $user = User::factory()->create(['family_members' => 5]);
        $room = Room::factory()->create(['capacity' => 1]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/reserves', [
            'room_id' => $room->id,
            'check_in_date' => '2023-10-01',
            'check_out_date' => '2023-10-05',
            'family_members' => $user->family_members,
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson(['message' => 'Your family members exceed room capacity.']);
    }

}
