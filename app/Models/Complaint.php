<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Room;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'report_id',
        'description',
        'photo_path',
        'status_repair',
        'handler_id',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'report_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }
}