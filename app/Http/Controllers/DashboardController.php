<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\Event;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Jika belum login, arahkan ke halaman login
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil jumlah data dari database
        $totalUser = User::count();
        $totalRoom = Room::count();
        $totalEvent = Event::count();
        $totalComplaint = Complaint::count();

        // Ambil 5 pengaduan terbaru untuk dashboard Admin dan Waka
$latestComplaints = Complaint::with(['room', 'reporter', 'handler'])
    ->latest()
    ->take(5)
    ->get();

        // Ambil agenda untuk bulan yang sedang berjalan
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $upcomingEvents = Event::with('room')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'asc')
            ->get();

        // Ambil seluruh event untuk kalender interaktif
        $calendarEvents = Event::all([
            'event_name',
            'date as start',
            'date as end'
        ]);

        // Data dasar untuk dashboard
       $data = compact(
    'totalUser',
    'totalRoom',
    'totalEvent',
    'totalComplaint',
    'upcomingEvents',
    'calendarEvents',
    'latestComplaints'
);

        // =====================================
        // DATA KHUSUS DASHBOARD PIC
        // =====================================

        if ((int) $user->role_id === 5) {

            // Ambil laporan yang ditugaskan kepada PIC
            $picComplaints = Complaint::with(['room', 'reporter'])
                ->where('handler_id', $user->id)
                ->latest()
                ->get();

            // Hitung total laporan
            $totalLaporan = $picComplaints->count();

            // Hitung laporan berdasarkan status
            $belumDiperbaiki = $picComplaints
                ->where('status_repair', 'Belum Diperbaiki')
                ->count();

            $menungguSparePart = $picComplaints
                ->where('status_repair', 'Menunggu Spare Part')
                ->count();

            $sudahDiperbaiki = $picComplaints
                ->where('status_repair', 'Sudah Diperbaiki')
                ->count();

            // Gabungkan data PIC ke dashboard
            $data = array_merge($data, compact(
                'picComplaints',
                'totalLaporan',
                'belumDiperbaiki',
                'menungguSparePart',
                'sudahDiperbaiki'
            ));
        }

        // =====================================
        // TENTUKAN DASHBOARD BERDASARKAN ROLE
        // =====================================

        
            return match ((int) $user->role_id) {
            1, 2 => view('admin.dashboard', $data),
            3, 4 => view('guru.dashboard', $data),
            5 => view('pic.dashboard', $data),
            default => abort(403),
            };

    }
}
