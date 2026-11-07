<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $rooms = Room::with('pic')->latest()->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $users = User::where('role_id', 5)
            ->orderBy('fullname')
            ->get();

        return view('admin.rooms.create', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'pic_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role_id', 5),
            ],
        ]);

        Room::create([
            'room_name' => $validated['room_name'],
            'capacity' => $validated['capacity'],
            'location' => $validated['location'],
            'is_active' => true,
            'pic_id' => $validated['pic_id'] ?? null,
        ]);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $users = User::where('role_id', 5)
            ->orderBy('fullname')
            ->get();

        return view('admin.rooms.edit', compact('room', 'users'));
    }

    public function update(Request $request, Room $room)
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'pic_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role_id', 5),
            ],
        ]);

        $room->update([
            'room_name' => $validated['room_name'],
            'capacity' => $validated['capacity'],
            'location' => $validated['location'],
            'pic_id' => $validated['pic_id'] ?? null,
        ]);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Data ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        abort_unless(in_array((int) Auth::user()->role_id, [1, 2]), 403);

        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }
}
