<?php

namespace Tests\Feature\Hotel;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteHotelTest extends TestCase
{
    /** @test */
    public function it_can_delete_a_hotel()
    {
        $response = $this->deleteJson('/api/v1/hotels/1');
        $response->assertStatus(204);
    }
}
