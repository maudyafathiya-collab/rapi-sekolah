<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RoleSeeder::class,]);

        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'fullname' => 'Admin RAPI',
            'phone_number' => '081234567890',
            'role_id' => 1,
        ]);

        User::create([
            'username' => 'pic_budi',
            'password' => Hash::make('budi123'),
            'fullname' => 'Budi Santoso',   
            'phone_number' => '0812121212121',
            'role_id' => 5,
        ]);

        User::create([
            'username' => 'pic_andi',
            'password' => Hash::make('andi123'),    
            'fullname' => 'Andi Wijaya',
            'phone_number' => '087654285321',
            'role_id' => 5,
        ]);

        User::create([
            'username' => 'waka',
            'password' => Hash::make('waka123'),
            'fullname' => 'Waka RAPI',
            'phone_number' => '081111111111',
            'role_id' => 2,
            ]);

        User::create([
            'username' => 'guru',
            'password' => Hash::make('guru123'),
            'fullname' => 'Guru RAPI',
            'phone_number' => '082222222222',
            'role_id' => 3,
        ]);

        User::create([
            'username' => 'organisasi',
            'password' => Hash::make('organisasi123'),
            'fullname' => 'Organisasi RAPI',
            'phone_number' => '083333333333',
            'role_id' => 4,
        ]); 

        $this->call([
            RoomSeeder::class,
            EventSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
