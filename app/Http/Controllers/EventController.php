<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Event::with(['room', 'reporter'])->latest();

        // Guru dan Organisasi hanya melihat pengajuan sendiri.
        if (in_array((int) $user->role_id, [3, 4])) {
            $query->where('report_id', $user->id);
        } elseif (!in_array((int) $user->role_id, [1, 2])) {
            abort(403);
        }

        $events = $query->get();

        return view('events.index', compact('events'));
    }

    public function calendar()
    {
        return view('calendar.index');
    }

    public function calendarEvents()
    {
        $events = Event::with(['room', 'reporter'])
            ->where('status', 'Disetujui')
            ->get();

        $data = $events->map(function ($event) {
            $roleId = (int) ($event->reporter->role_id ?? 0);

            $color = match ($roleId) {
                1, 2 => '#9333EA',
                3 => '#16A34A',
                4 => '#2563EB',
                default => '#64748B',
            };

            $jenisPengaju = match ($roleId) {
                1 => 'Admin',
                2 => 'Waka',
                3 => 'Guru',
                4 => 'Organisasi Siswa',
                default => 'Pengguna',
            };

            return [
                'id' => $event->id,
                'title' => $event->event_name,
                'start' => $event->date->format('Y-m-d') . 'T' . $event->start_time,
                'end' => $event->date->format('Y-m-d') . 'T' . $event->end_time,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#FFFFFF',
                'room' => $event->room->room_name ?? '-',
                'status' => $event->status,
                'jenis_pengaju' => $jenisPengaju,
            ];
        });

        return response()->json($data);
    }

    public function calendarHolidays()
    {
        $holidays = DB::table('holidays')
            ->select('name', 'date', 'type')
            ->orderBy('date')
            ->get();

        return response()->json($holidays);
    }

    public function create()
    {
        if (!in_array((int) Auth::user()->role_id, [1, 2, 3, 4])) {
            abort(403);
        }

        $rooms = Room::where('is_active', true)->get();

        return view('events.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        if (!in_array((int) Auth::user()->role_id, [1, 2, 3, 4])) {
            abort(403);
        }

        $request->validate([
            'event_name' => 'required|string|max:255',
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where('is_active', true),
            ],
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'surat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'event_name.required' => 'Nama kegiatan wajib diisi.',
            'room_id.required' => 'Silakan pilih ruangan.',
            'room_id.exists' => 'Ruangan tidak tersedia atau sudah tidak aktif.',
            'date.after_or_equal' => 'Tanggal kegiatan tidak boleh sebelum hari ini.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'surat.required' => 'Surat peminjaman wajib diunggah.',
            'surat.mimes' => 'Surat harus berformat PDF, JPG, JPEG, atau PNG.',
            'surat.max' => 'Ukuran surat maksimal 2 MB.',
        ]);

        // CEK BENTROK SAAT USER MENGAJUKAN PEMINJAMAN.
        $conflict = Event::where('room_id', $request->room_id)
            ->whereDate('date', $request->date)
            ->where('status', 'Disetujui')
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors([
                    'room_id' => 'Ruangan sudah dipinjam pada tanggal dan jam tersebut. Silakan pilih ruangan atau waktu lain.',
                ])
                ->withInput();
        }

        // Surat baru disimpan setelah pengajuan lolos pengecekan bentrok.
        $suratPath = $request->file('surat')->store(
            'surat-peminjaman',
            'public'
        );

        Event::create([
            'event_name' => $request->event_name,
            'room_id' => $request->room_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'report_id' => Auth::id(),
            'status' => 'Pending',
            'approved_by' => null,
            'rejection_reason' => null,
            'move_reason' => null,
            'surat_path' => $suratPath,
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan.');
    }

    public function edit($id)
    {
        if (!in_array((int) Auth::user()->role_id, [1, 2])) {
            abort(403);
        }

        $event = Event::with(['room', 'reporter'])->findOrFail($id);

        $rooms = Room::where('is_active', true)
            ->orWhere('id', $event->room_id)
            ->get();

        return view('events.edit', compact('event', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        // Hanya Admin dan Waka yang boleh memverifikasi pengajuan.
        if (!in_array((int) Auth::user()->role_id, [1, 2])) {
            abort(403);
        }

        $event = Event::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak',
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where('is_active', true),
            ],
            'rejection_reason' => 'nullable|required_if:status,Ditolak|string|max:500',
            'move_reason' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Status pengajuan wajib dipilih.',
            'room_id.required' => 'Silakan pilih ruangan.',
            'room_id.exists' => 'Ruangan tidak tersedia atau sudah tidak aktif.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        $roomChanged = (int) $request->room_id !== (int) $event->room_id;

        // Jika ruangan dipindahkan, alasan pemindahan wajib diisi.
        if ($roomChanged && !$request->filled('move_reason')) {
            return back()
                ->withErrors([
                    'move_reason' => 'Alasan pemindahan ruangan wajib diisi.',
                ])
                ->withInput();
        }

        // PENGECEKAN KEDUA: PENGAMAN SAAT ADMIN/WAKA MENYETUJUI.
        if ($request->status === 'Disetujui') {
            $conflict = Event::where('room_id', $request->room_id)
                ->whereDate('date', $event->date)
                ->where('status', 'Disetujui')
                ->where('id', '!=', $event->id)
                ->where('start_time', '<', $event->end_time)
                ->where('end_time', '>', $event->start_time)
                ->exists();

            if ($conflict) {
                return back()
                    ->withErrors([
                        'room_id' => 'Ruangan sudah digunakan pada jam tersebut. Silakan pilih ruangan atau waktu lain.',
                    ])
                    ->withInput();
            }
        }

        $event->room_id = $request->room_id;
        $event->status = $request->status;

        $event->rejection_reason = $request->status === 'Ditolak'
            ? $request->rejection_reason
            : null;

        $event->move_reason = $roomChanged
            ? $request->move_reason
            : $event->move_reason;

        $event->approved_by = Auth::id();

        $event->save();

        return redirect()
            ->route('events.index')
            ->with('success', 'Pengajuan peminjaman berhasil diperbarui.');
    }
}
