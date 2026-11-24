<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Support\Facades\Auth;

class PicRoomController extends Controller
{
    public function index()
    {
        // Pastikan hanya PIC yang bisa mengakses halaman ini
        abort_unless((int) Auth::user()->role_id === 5, 403);

        // Tampilkan hanya ruangan yang menjadi tanggung jawab PIC login
        $rooms = Room::where('pic_id', Auth::id())
            ->orderBy('room_name')
            ->get();

        return view('pic.rooms.index', compact('rooms'));
    }
}
