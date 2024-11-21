<?php

namespace Tests\Feature\Reservation;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Tests\TestCase;

class UpdateReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_not_authorized_to_update_reservation()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user1->id,
            'room_id' => $room->id,
            'check_in_date' => '2023-10-01',
            'check_out_date' => '2023-10-05',
        ]);

        // Act
        $response = $this->actingAs($user2)->putJson("/api/v1/reserves/{$reservation->id}", [
            'room_id' => $room->id,
            'check_in_date' => '2023-10-02',
            'check_out_date' => '2023-10-06',
        ]);

        // Assert
        $response->assertStatus(403)
            ->assertJson(['message' => 'شما مجاز به ویرایش این رزرو نیستید.']);
    }


    public function test_reservation_date_conflict()
    {
        // Arrange
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $existingReservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_date' => '2023-10-01',
            'check_out_date' => '2023-10-05',
        ]);
        $reservationToUpdate = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_date' => '2023-10-06',
            'check_out_date' => '2023-10-10',
        ]);

        // Act
        $response = $this->actingAs($user)->putJson("/api/v1/reserves/{$reservationToUpdate->id}", [
            'room_id' => $room->id,
            'check_in_date' => '2023-10-04',
            'check_out_date' => '2023-10-07',
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson(['message' => 'این اتاق در تاریخ‌های انتخابی رزرو شده است.']);
    }



    public function test_successful_reservation_update()
    {
        // Arrange
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id, // کاربر باید همان کاربر باشد
            'room_id' => $room->id,
            'check_in_date' => '2023-10-01',
            'check_out_date' => '2023-10-05',
        ]);

        // Act
        $response = $this->actingAs($user)->putJson("/api/v1/reserves/{$reservation->id}", [
            'room_id' => $room->id,
            'check_in_date' => '2023-10-06',
            'check_out_date' => '2023-10-10',
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'check_in_date' => '2023-10-06',
            'check_out_date' => '2023-10-10',
        ]);
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'reserved',
        ]);
    }

}
