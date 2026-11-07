
@extends('layouts.pic')

@section('title', 'Dashboard PIC | RAPI Sekolah')
@section('page_title', 'Dashboard PIC')
@section('page_subtitle', 'Panel pengelolaan pengaduan ruangan')

@section('content')

    {{-- Sambutan --}}
    <div class="mb-4">
        <h4>Selamat Datang, {{ Auth::user()->fullname }}!</h4>
        <p class="text-muted">
            Berikut laporan kerusakan yang menjadi tanggung jawabmu.
        </p>
    </div>

    {{-- Ringkasan pengaduan --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Laporan</p>
                            <h3 class="fw-bold mb-0">{{ $totalLaporan }}</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        Pengaduan yang ditugaskan kepadamu
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Belum Diperbaiki</p>
                            <h3 class="fw-bold text-danger mb-0">
                                {{ $belumDiperbaiki }}
                            </h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Menunggu Spare Part</p>
                            <h3 class="fw-bold text-warning mb-0">
                                {{ $menungguSparePart }}
                            </h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Sudah Diperbaiki</p>
                            <h3 class="fw-bold text-success mb-0">
                                {{ $sudahDiperbaiki }}
                            </h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Daftar pengaduan --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="mb-3">
                <h5 class="mb-1">Daftar Pengaduan Saya</h5>
                <p class="text-muted mb-0">
                    Laporan kerusakan yang perlu ditangani.
                </p>
            </div>

            <div class="table-responsive">

                <table class="table table-hover table-striped align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Ruangan</th>
                            <th>Deskripsi Kerusakan</th>
                            <th>Pelapor</th>
                            <th>Tanggal Laporan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($picComplaints as $index => $complaint)

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $complaint->room->room_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $complaint->description }}
                                </td>

                                <td>
                                    {{ $complaint->reporter->fullname ?? '-' }}
                                </td>

                                <td>
                                    {{ $complaint->created_at->format('d-m-Y') }}
                                </td>

                                <td>
                                    @if ($complaint->status_repair === 'Belum Diperbaiki')
                                        <span class="badge bg-danger">
                                            Belum Diperbaiki
                                        </span>
                                    @elseif ($complaint->status_repair === 'Menunggu Spare Part')
                                        <span class="badge bg-warning text-dark">
                                            Menunggu Spare Part
                                        </span>
                                    @elseif ($complaint->status_repair === 'Sudah Diperbaiki')
                                        <span class="badge bg-success">
                                            Sudah Diperbaiki
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Belum Ada Status
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a
                                        href="{{ route('complaints.edit', $complaint->id) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        Perbarui
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-clipboard-check fs-2 d-block mb-2 text-muted"></i>
                                    Belum ada pengaduan yang ditugaskan kepadamu.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

@endsection
