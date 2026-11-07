<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    // Menampilkan daftar pengaduan sesuai hak akses pengguna
    
public function index()
{
    $user = Auth::user();

    $query = Complaint::with(['room', 'reporter', 'handler']);

    // PIC hanya melihat pengaduan yang ditugaskan kepadanya
    if ((int) $user->role_id === 5) {
        $query->where('handler_id', $user->id);
    }

    // Guru dan Organisasi hanya melihat laporan miliknya
    if (in_array((int) $user->role_id, [3, 4])) {
        $query->where('report_id', $user->id);
    }

    // Hanya Admin, Waka, Guru, Organisasi, dan PIC
    abort_unless(
        in_array((int) $user->role_id, [1, 2, 3, 4, 5]),
        403
    );

    $complaints = $query->latest()->get();

    return view('complaints.index', compact('complaints'));
}


    // Form tambah pengaduan
    public function create()
    {
        $user = Auth::user();

        // Hanya Guru dan Organisasi yang boleh membuat pengaduan
        abort_unless(
            in_array((int) $user->role_id, [3, 4]),
            403
        );

        $rooms = Room::where('is_active', true)->get();

        // Ambil data pengguna yang sedang login saja
        $users = User::where('id', $user->id)->get();

        return view('complaints.create', compact('rooms', 'users'));
    }

    // Menyimpan pengaduan baru
    
public function store(Request $request)
{
    $user = Auth::user();

    // Hanya Guru dan Organisasi yang boleh mengirim laporan
    abort_unless(
        in_array((int) $user->role_id, [3, 4]),
        403
    );

    $validated = $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'description' => 'required|string',
        'photo_path' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Ambil ruangan beserta PIC yang bertanggung jawab
    $room = Room::with('pic')
        ->where('is_active', true)
        ->find($validated['room_id']);

    // Pastikan ruangan aktif
    if (!$room) {
        return back()
            ->withErrors([
                'room_id' => 'Ruangan tidak aktif atau tidak ditemukan.',
            ])
            ->withInput();
    }

    // Pastikan ruangan sudah memiliki PIC dengan role 5
    if (!$room->pic || (int) $room->pic->role_id !== 5) {
        return back()
            ->withErrors([
                'room_id' => 'PIC untuk ruangan ini belum ditentukan. Silakan hubungi Admin atau Waka.',
            ])
            ->withInput();
    }

    // Simpan foto hasil kamera
    $validated['photo_path'] = $request->file('photo_path')
        ->store('complaints', 'public');

    // Identitas pelapor diambil dari akun yang sedang login
    $validated['report_id'] = $user->id;

    // PIC otomatis berdasarkan PIC ruangan
    $validated['handler_id'] = $room->pic_id;

    // Status awal pengaduan
    $validated['status_repair'] = 'Belum Diperbaiki';

    Complaint::create($validated);

    return redirect()
        ->route('complaints.index')
        ->with('success', 'Pengaduan berhasil ditambahkan dan otomatis diteruskan kepada PIC ruangan.');
}


    // Menampilkan detail pengaduan
    public function edit(Complaint $complaint)
    {
        $user = Auth::user();

        $roleId = (int) $user->role_id;

        // PIC hanya boleh membuka pengaduan yang ditugaskan kepadanya
        if ($roleId === 5) {
            abort_unless(
                (int) $complaint->handler_id === (int) $user->id,
                403,
                'Pengaduan ini bukan tugas Anda.'
            );
        }

        // Guru dan Organisasi hanya boleh membuka laporan sendiri
        if (in_array($roleId, [3, 4])) {
            abort_unless(
                (int) $complaint->report_id === (int) $user->id,
                403
            );
        }

        // Batasi role yang boleh mengakses detail pengaduan
        abort_unless(
            in_array($roleId, [1, 2, 3, 4, 5]),
            403
        );

        $complaint->load(['room', 'reporter', 'handler']);

        return view('complaints.edit', compact('complaint'));
    }

    // Memperbarui status perbaikan oleh PIC
    public function update(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Hanya PIC yang boleh memperbarui status
        abort_unless(
            (int) $user->role_id === 5,
            403
        );

        // PIC hanya boleh memperbarui tugas miliknya
        abort_unless(
            (int) $complaint->handler_id === (int) $user->id,
            403,
            'Pengaduan ini bukan tugas Anda.'
        );

        $validated = $request->validate([
            'status_repair' => 'required|in:Belum Diperbaiki,Menunggu Spare Part,Sudah Diperbaiki',
        ]);

        // Hanya kolom status_repair yang diperbarui
        $complaint->update([
            'status_repair' => $validated['status_repair'],
        ]);

        return redirect()
            ->route('complaints.index')
            ->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    // Riwayat pengaduan yang sudah diperbaiki
    public function history()
    {
        $user = Auth::user();

        $roleId = (int) $user->role_id;

        // Hanya role yang berhak melihat riwayat
        abort_unless(
            in_array($roleId, [1, 2, 3, 4, 5]),
            403
        );

        $query = Complaint::with(['room', 'reporter', 'handler'])
            ->where('status_repair', 'Sudah Diperbaiki');

        // PIC hanya melihat riwayat tugasnya sendiri
        if ($roleId === 5) {
            $query->where('handler_id', $user->id);
        }

        // Guru dan Organisasi hanya melihat laporan sendiri
        if (in_array($roleId, [3, 4])) {
            $query->where('report_id', $user->id);
        }

        $complaints = $query->latest()->get();

        return view('complaints.history', compact('complaints'));
    }
}
