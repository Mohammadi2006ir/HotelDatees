<?php

namespace Tests\Feature\Hotel;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateHotelTest extends TestCase
{
    /** @test */
    public function it_can_update_a_hotel()
    {
        // داده‌های جدید برای به‌روزرسانی
        $data = [
            'name' => 'Pars',
            'city' => 'Tehran',
            'star_rating' => 5,
        ];

        // به‌روزرسانی هتل
        $response = $this->putJson('/api/v1/hotels/1', $data);

        // بررسی وضعیت پاسخ
        $response->assertStatus(200);
        // بررسی اینکه هتل با داده‌های جدید به‌روزرسانی شده است
        $this->assertDatabaseHas('hotels', $data);
    }

    /** @test */
    public function it_returns_404_when_hotel_not_found_on_update()
    {
        $data = [
            'name' => 'Pars',
            'city' => 'Tehran',
            'star_rating' => 5,
        ];

        // تلاش برای به‌روزرسانی هتل غیرموجود
        $response = $this->putJson('/api/v1/hotels/999', $data);

        // بررسی وضعیت پاسخ
        $response->assertStatus(404); // 404 Not Found
    }

}
