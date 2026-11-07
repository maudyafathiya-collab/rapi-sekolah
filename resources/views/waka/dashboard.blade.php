@extends('layouts.waka')

@section('title', 'Dashboard Waka | RAPI Sekolah')

@section('page_title', 'Dashboard Waka')

@section('page_subtitle', 'Panel Operasional Sekolah')

@section('content')

    {{-- Banner Selamat Datang --}}
    <div class="card mb-4 border-0 bg-primary text-white shadow-sm">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-1 fs-5">
                Selamat Datang, {{ Auth::user()->fullname }}
            </h4>

            <p class="mb-0 opacity-75 small">
                Sistem manajemen sarana prasarana dan peminjaman ruangan
                terintegrasi RAPI Sekolah.
            </p>

        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Total Pengguna
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalUser ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Total Ruangan
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalRoom ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-door-open"></i>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Total Agenda
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalEvent ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Total Pengaduan
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalComplaint ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Agenda Sekolah --}}
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h6 class="fw-bold mb-0">
                            Agenda Sekolah Bulan Ini
                        </h6>

                        <a href="{{ route('events.index') }}"
                           class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>

                    </div>

                    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Agenda</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($upcomingEvents as $index => $event)

                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                {{ $event->event_name }}
                                            </td>

                                            <td>
                                                {{ $event->room->room_name ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $event->date->format('d M Y') }}
                                            </td>

                                            <td>

                                                @if($event->status === 'Disetujui')

                                                    <span class="badge bg-success">
                                                        Disetujui
                                                    </span>

                                                @elseif($event->status === 'Ditolak')

                                                    <span class="badge bg-danger">
                                                        Ditolak
                                                    </span>

                                                @else

                                                    <span class="badge bg-warning text-dark">
                                                        Pending
                                                    </span>

                                                @endif

                                            </td>
                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="text-center py-4 text-muted small">

                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>

                            Belum ada agenda untuk bulan ini.

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- Informasi Pengaduan --}}
    <div class="card mt-4">

        <div class="card-body p-4">

            <h6 class="fw-bold mb-2">
                Pengelolaan Pengaduan
            </h6>

            <p class="text-muted small">
                Periksa laporan kerusakan, tentukan PIC yang bertanggung jawab,
                dan pantau riwayat perbaikan.
            </p>

            <a href="{{ route('complaints.index') }}"
               class="btn btn-primary btn-sm me-2">
                Kelola Pengaduan
            </a>

            <a href="{{ route('complaints.history') }}"
               class="btn btn-outline-primary btn-sm">
                Riwayat Pengaduan
            </a>

        </div>

    </div>

@endsection
```