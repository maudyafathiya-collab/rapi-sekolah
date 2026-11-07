<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'event_name' => 'Rapat Koordinasi',
            'date' => '2024-06-15',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'room_id' => 1,
        ]);

        Event::create([
            'event_name' => 'Pelatihan Guru',
            'date' => '2024-06-20',
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'room_id' => 2,
        ]);

        Event::create([
            'event_name' => 'Pertemuan Organisasi',
            'date' => '2024-06-25',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'room_id' => 3,
        ]);

        Event::create([
            'event_name' => 'Workshop Teknologi',
            'date' => '2024-07-01',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'room_id' => 4,
        ]);

        Event::create([
            'event_name' => 'Seminar Pendidikan',
            'date' => '2024-07-05',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'room_id' => 5,
        ]);
    }
}
