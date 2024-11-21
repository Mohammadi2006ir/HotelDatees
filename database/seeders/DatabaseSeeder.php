<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'ali',
            'email' => 'ali@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        User::factory()->count(9)->create();

        $this->call(RolesAndPermissionsSeeder::class);

        Hotel::factory()->count(10)->create();
        Room::factory()->count(50)->create();
        Reservation::factory()->count(15)->create();
    }
}
