
@extends('layouts.guru')

@section('title', 'Dashboard | RAPI Sekolah')

@section('page_title', 'Dashboard')

@section('page_subtitle', 'Panel Guru dan Organisasi')

@section('content')

    {{-- Banner Selamat Datang --}}
    <div class="card mb-4 border-0 bg-primary text-white shadow-sm">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-1 fs-5">
                Selamat Datang, {{ Auth::user()->fullname }}
            </h4>

            <p class="mb-0 opacity-75 small">
                Selamat datang di RAPI Sekolah.
                Kelola pengajuan agenda dan pantau pengaduan fasilitas sekolah
                melalui satu sistem terintegrasi.
            </p>

        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-4">
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

        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Agenda Saya
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalAgendaSaya ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <span class="text-muted small d-block mb-1">
                            Pengaduan Saya
                        </span>

                        <h4 class="fw-bold mb-0">
                            {{ $totalComplaintSaya ?? 0 }}
                        </h4>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Tombol Aksi Cepat --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>

                        <div>
                            <h6 class="fw-bold mb-1">Ajukan Agenda</h6>
                            <small class="text-muted">
                                Ajukan peminjaman ruangan untuk kegiatan sekolah.
                            </small>
                        </div>
                    </div>

                    <a href="{{ route('events.create') }}"
                       class="btn btn-primary btn-sm w-100">
                        Buat Pengajuan
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon">
                            <i class="bi bi-tools"></i>
                        </div>

                        <div>
                            <h6 class="fw-bold mb-1">Laporkan Kerusakan</h6>
                            <small class="text-muted">
                                Laporkan kerusakan fasilitas sekolah.
                            </small>
                        </div>
                    </div>

                    <a href="{{ route('complaints.create') }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        Buat Pengaduan
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- Agenda Saya --}}
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h6 class="fw-bold mb-0">
                            Agenda Saya
                        </h6>

                        <a href="{{ route('events.index') }}"
                           class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>

                    </div>

                    @if(isset($myEvents) && $myEvents->count() > 0)

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

                                    @foreach($myEvents as $index => $event)

                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td>{{ $event->event_name }}</td>

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

                            Kamu belum memiliki pengajuan agenda.

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection
