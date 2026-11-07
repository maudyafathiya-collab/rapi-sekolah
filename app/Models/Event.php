<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'report_id',
        'event_name',
        'surat_path',
        'date',
        'start_time',
        'end_time',
        'status',
        'approved_by',
        'rejection_reason',
        'move_reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'report_id');
    }
}
