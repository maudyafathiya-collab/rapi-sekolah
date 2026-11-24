<?php

namespace App\Http\Controllers;

use App\Models\BookingSession;
use Illuminate\Http\Request;

class BookingSessionController extends Controller
{
    // Menampilkan daftar sesi
    public function index()
    {
        $sessions = BookingSession::orderBy('day_type')
            ->orderBy('start_time')
            ->get();

        return view('booking_sessions.index', compact('sessions'));
    }

    // Menyimpan sesi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'day_type' => 'required|in:sekolah,libur',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        BookingSession::create($validated + [
            'is_active' => true,
        ]);

        return back()->with('success', 'Sesi berhasil ditambahkan.');
    }

    // Memperbarui sesi
    public function update(Request $request, BookingSession $bookingSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'day_type' => 'required|in:sekolah,libur',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $bookingSession->update($validated);

        return back()->with('success', 'Sesi berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan sesi
    public function toggle(BookingSession $bookingSession)
    {
        $bookingSession->update([
            'is_active' => !$bookingSession->is_active,
        ]);

        return back()->with('success', 'Status sesi berhasil diperbarui.');
    }

    // Menghapus sesi
    public function destroy(BookingSession $bookingSession)
    {
        $bookingSession->delete();

        return back()->with('success', 'Sesi berhasil dihapus.');
    }
}
