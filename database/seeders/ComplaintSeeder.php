<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Complaint;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Complaint::create([
            'room_id' => 1,
            'report_id' => 1,
            'description' => 'Kursi rusak',
            'photo_path' => null,
            'status_repair' => 'Belum Diperbaiki',
            'handler_id' => null,
        ]);
    }
}
