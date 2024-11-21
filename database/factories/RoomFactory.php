<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => $this->faker->numberBetween(1, 10), // استفاده از فکتوری برای مدل Hotel
            'room_type' => $this->faker->word,
            'capacity' => $this->faker->numberBetween(1, 4),
            'price' => $this->faker->randomFloat(2, 50, 300),
        ];
    }
}
