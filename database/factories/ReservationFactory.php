<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => $this->faker->numberBetween(1, 50),
            'user_id' => $this->faker->numberBetween(1, 10),
            'check_in_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'), // تاریخ ورود
            'check_out_date' => $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'), // تاریخ خروج
        ];
    }
}
