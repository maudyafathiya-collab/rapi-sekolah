<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Pastikan kolom-kolom ini diizinkan untuk diisi (mass assignment)
    protected $fillable = [
        'room_name',
        'capacity',
        'location',
        'is_active',
        'pic_id', // <-- Tambahkan pic_id di sini
    ];

    // Relasi ke model User (untuk mengambil data PIC tetap ruangan)
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}