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


    /*
    |--------------------------------------------------------------------------
    | DAFTAR AGENDA YANG DAPAT DIPINDAHKAN
    |--------------------------------------------------------------------------
    */

    public function moveRoomIndex()
    {
        // Hanya Admin dan Waka.
        if (!in_array((int) Auth::user()->role_id, [1, 2])) {
            abort(403);
        }

        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // Hanya agenda Disetujui dan belum dimulai.
        $events = Event::with(['room', 'reporter'])
            ->where('status', 'Disetujui')
            ->where(function ($query) use ($today, $currentTime) {
                $query->whereDate('date', '>', $today)
                    ->orWhere(function ($query) use ($today, $currentTime) {
                        $query->whereDate('date', $today)
                            ->where('start_time', '>', $currentTime);
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('events.move-room.index', compact('events'));
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULIR PEMINDAHAN RUANGAN
    |--------------------------------------------------------------------------
    */

    public function moveRoomEdit($id)
    {
        // Hanya Admin dan Waka.
        if (!in_array((int) Auth::user()->role_id, [1, 2])) {
            abort(403);
        }

        $event = Event::with(['room', 'reporter'])->findOrFail($id);

        // Pastikan agenda sudah disetujui.
        if ($event->status !== 'Disetujui') {
            return redirect()
                ->route('events.move-room.index')
                ->with('error', 'Hanya agenda yang sudah disetujui yang dapat dipindahkan.');
        }

        // Periksa tanggal dan waktu mulai kegiatan.
        $eventStart = \Carbon\Carbon::parse(
            \Carbon\Carbon::parse($event->date)->format('Y-m-d')
            . ' ' . $event->start_time
        );

        // Agenda yang sudah dimulai tidak boleh dipindahkan.
        if (now()->greaterThanOrEqualTo($eventStart)) {
            return redirect()
                ->route('events.move-room.index')
                ->with('error', 'Agenda tidak dapat dipindahkan karena sudah dimulai atau selesai.');
        }

        // Ambil ruangan aktif selain ruangan saat ini.
        $rooms = Room::where('is_active', true)
            ->where('id', '!=', $event->room_id)
            ->orderBy('room_name')
            ->get();

        return view('events.move-room.edit', compact('event', 'rooms'));
    }


    /*
    |--------------------------------------------------------------------------
    | KALENDER
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | FORMULIR PENGAJUAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!in_array((int) Auth::user()->role_id, [1, 2, 3, 4])) {
            abort(403);
        }

        $rooms = Room::where('is_active', true)->get();

        return view('events.create', compact('rooms'));
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PENGAJUAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!in_array((int) Auth::user()->role_id, [1, 2, 3, 4])) {
            abort(403);
        }

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',

            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where('is_active', true),
            ],

            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',

            'surat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'event_name.required' => 'Nama kegiatan wajib diisi.',
            'room_id.required' => 'Silakan pilih ruangan.',
            'room_id.exists' => 'Ruangan tidak tersedia atau sudah tidak aktif.',
            'date.after' => 'Pengajuan peminjaman harus dilakukan minimal H-1. Tanggal kegiatan paling cepat adalah besok.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'surat.required' => 'Surat peminjaman wajib diunggah.',
            'surat.mimes' => 'Surat harus berformat PDF, JPG, JPEG, atau PNG.',
            'surat.max' => 'Ukuran surat maksimal 2 MB.',
        ]);

        // Memastikan jam selesai setelah jam mulai.
        if ($validated['end_time'] <= $validated['start_time']) {
            return back()
                ->withErrors([
                    'end_time' => 'Jam selesai harus setelah jam mulai.',
                ])
                ->withInput();
        }

        // Memeriksa bentrok ruangan.
        $conflict = Event::where('room_id', $validated['room_id'])
            ->whereDate('date', $validated['date'])
            ->whereIn('status', ['Pending', 'Disetujui'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors([
                    'start_time' => 'Ruangan sudah memiliki pengajuan atau peminjaman pada jam tersebut. Silakan pilih jam lain.',
                ])
                ->withInput();
        }

        $suratPath = $request->file('surat')
            ->store('surat-peminjaman', 'public');

        Event::create([
            'event_name' => $validated['event_name'],
            'room_id' => $validated['room_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
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


    /*
    |--------------------------------------------------------------------------
    | EDIT / VERIFIKASI PENGAJUAN
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | UPDATE PENGAJUAN DAN PEMINDAHAN RUANGAN
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        // Hanya Admin dan Waka.
        if (!in_array((int) Auth::user()->role_id, [1, 2])) {
            abort(403);
        }

        $event = Event::with(['room', 'reporter'])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | AKSI 1: PINDAHKAN RUANGAN
        |--------------------------------------------------------------------------
        */

        if ($request->input('action') === 'move_room') {

            // Pemindahan hanya boleh untuk agenda yang disetujui.
            if ($event->status !== 'Disetujui') {
                return back()
                    ->with('error', 'Hanya agenda yang sudah disetujui yang dapat dipindahkan.')
                    ->withInput();
            }

            // Periksa apakah agenda sudah dimulai.
            $eventStart = \Carbon\Carbon::parse(
                \Carbon\Carbon::parse($event->date)->format('Y-m-d')
                . ' ' . $event->start_time
            );

            if (now()->greaterThanOrEqualTo($eventStart)) {
                return back()
                    ->with('error', 'Agenda tidak dapat dipindahkan karena sudah dimulai atau selesai.')
                    ->withInput();
            }

            $validated = $request->validate([
                'room_id' => [
                    'required',
                    Rule::exists('rooms', 'id')->where('is_active', true),
                ],
                'move_reason' => 'required|string|max:500',
            ], [
                'room_id.required' => 'Silakan pilih ruangan baru.',
                'room_id.exists' => 'Ruangan tidak tersedia atau sudah tidak aktif.',
                'move_reason.required' => 'Alasan pemindahan ruangan wajib diisi.',
            ]);

            // Pastikan ruangan berbeda.
            if ((int) $validated['room_id'] === (int) $event->room_id) {
                return back()
                    ->with('error', 'Pilih ruangan yang berbeda untuk melakukan pemindahan.')
                    ->withInput();
            }

            // Simpan nama ruangan lama.
            $oldRoomName = $event->room->room_name ?? '-';

            // Periksa bentrok pada ruangan tujuan.
            $conflict = Event::where('room_id', $validated['room_id'])
                ->whereDate('date', $event->date)
                ->whereIn('status', ['Pending', 'Disetujui'])
                ->where('id', '!=', $event->id)
                ->where('start_time', '<', $event->end_time)
                ->where('end_time', '>', $event->start_time)
                ->exists();

            if ($conflict) {
                return back()
                    ->withErrors([
                        'room_id' => 'Ruangan tujuan sudah memiliki pengajuan atau peminjaman pada jam tersebut. Silakan pilih ruangan lain.',
                    ])
                    ->withInput();
            }

            // Simpan ruangan baru dan alasan pemindahan.
            $event->room_id = $validated['room_id'];
            $event->move_reason = $validated['move_reason'];
            $event->save();

            // Ambil data terbaru.
            $event->load(['room', 'reporter']);

            $reporter = $event->reporter;

            // Notifikasi ditujukan kepada Guru dan Organisasi Siswa.
            if (
                $reporter &&
                in_array((int) $reporter->role_id, [3, 4])
            ) {
                // Normalisasi nomor WhatsApp.
                $phone = preg_replace(
                    '/\D+/',
                    '',
                    (string) $reporter->phone_number
                );

                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                } elseif (str_starts_with($phone, '8')) {
                    $phone = '62' . $phone;
                }

                if (!empty($phone)) {
                    $tanggal = \Carbon\Carbon::parse($event->date)
                        ->format('d-m-Y');

                    $message =
                        "Halo {$reporter->fullname}, kami ingin memberitahukan "
                        . "bahwa ruangan untuk kegiatan peminjaman Anda mengalami pemindahan.\n\n"
                        . "Nama kegiatan: {$event->event_name}\n"
                        . "Ruangan sebelumnya: {$oldRoomName}\n"
                        . "Ruangan baru: {$event->room->room_name}\n"
                        . "Tanggal: {$tanggal}\n"
                        . "Waktu: {$event->start_time} - {$event->end_time}\n"
                        . "Alasan pemindahan: {$event->move_reason}\n\n"
                        . "Terima kasih.";

                    $whatsappUrl = 'https://wa.me/' . $phone
                        . '?text=' . urlencode($message);

                    return redirect()
                        ->route('events.index')
                        ->with('success', 'Ruangan berhasil dipindahkan. Silakan kirim pemberitahuan kepada pemohon.')
                        ->with('whatsapp_url', $whatsappUrl);
                }
            }

            return redirect()
                ->route('events.index')
                ->with('success', 'Ruangan berhasil dipindahkan, tetapi nomor WhatsApp pemohon belum tersedia.');
        }


        /*
        |--------------------------------------------------------------------------
        | AKSI 2: SIMPAN KEPUTUSAN PERSETUJUAN
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak',
            'rejection_reason' => 'nullable|required_if:status,Ditolak|string|max:500',
        ], [
            'status.required' => 'Status pengajuan wajib dipilih.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        // Periksa bentrok jika pengajuan akan disetujui.
        if ($validated['status'] === 'Disetujui') {
            $conflict = Event::where('room_id', $event->room_id)
                ->whereDate('date', $event->date)
                ->where('status', 'Disetujui')
                ->where('id', '!=', $event->id)
                ->where('start_time', '<', $event->end_time)
                ->where('end_time', '>', $event->start_time)
                ->exists();

            if ($conflict) {
                return back()
                    ->withErrors([
                        'status' => 'Ruangan sudah digunakan pada jam tersebut. Pengajuan tidak dapat disetujui.',
                    ])
                    ->withInput();
            }
        }

        // Simpan keputusan tanpa mengubah ruangan.
        $event->status = $validated['status'];

        $event->rejection_reason = $validated['status'] === 'Ditolak'
            ? $validated['rejection_reason']
            : null;

        $event->approved_by = Auth::id();

        $event->save();

        return redirect()
            ->route('events.index')
            ->with('success', 'Keputusan peminjaman berhasil diperbarui.');
    }
}
