<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::create([
            'room_name' => 'Wijaya Kusuma',
            'location' => 'Depan Taman',
            'capacity' => 135,
        ]);

        Room::create([
            'room_name' => 'DAK',
            'location' => 'Samping Kafetaria',
            'capacity' => 40,
        ]);

        Room::create([
            'room_name' => 'Ruang 1',
            'location' => 'Selatan Aula Bawah',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 2',
            'location' => 'Selatan Ruang 1',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 3',
            'location' => 'Selatan Ruang 2',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 4',
            'location' => 'Selatan Ruang 3',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 5',
            'location' => 'Selatan Ruang 4',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 6',
            'location' => 'Selatan Ruang 5',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 7',
            'location' => 'Selatan Ruang 6',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 8',
            'location' => 'Selatan Ruang 7',
            'capacity' => 36,
        ]);

        Room::create([
            'room_name' => 'Ruang 9',
            'location' => 'Selatan Ruang 8',
            'capacity' => 36,
        ]);
    }
}
