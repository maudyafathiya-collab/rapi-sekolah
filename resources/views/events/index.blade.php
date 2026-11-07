
@php
    $role = Auth::check() ? (int) Auth::user()->role_id : null;

    $layout = match ($role) {
        1 => 'layouts.admin',
        2 => 'layouts.waka',
        3, 4 => 'layouts.guru',
        5 => 'layouts.pic',
        default => 'layouts.public',
    };

    $isAdminOrWaka = in_array($role, [1, 2]);
    $canApply = in_array($role, [1, 2, 3, 4]);
@endphp

@extends($layout)

@section('title', 'Daftar Peminjaman | RAPI Sekolah')
@section('page_title', 'Daftar Peminjaman')
@section('page_subtitle', 'Kelola dan pantau jadwal peminjaman ruangan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Peminjaman Ruangan</h4>
        <p class="text-muted small mb-0">
            Pantau pengajuan peminjaman ruangan.
        </p>
    </div>

    @if($canApply)
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Ajukan Peminjaman
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Nama Kegiatan</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Alasan Penolakan</th>

                        @if($isAdminOrWaka)
                            <th class="text-end pe-3">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($events as $index => $event)
                        <tr>
                            <td class="ps-3">{{ $index + 1 }}</td>

                            <td class="fw-medium">
                                {{ $event->event_name }}
                            </td>

                            <td>
                                {{ $event->room->room_name ?? '-' }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                            </td>

                            <td>
                                {{ substr($event->start_time, 0, 5) }}
                                -
                                {{ substr($event->end_time, 0, 5) }}
                            </td>

                            <td>
                                @if($event->status === 'Disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($event->status === 'Ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>

                            <td>
                                @if($event->status === 'Ditolak')
                                    {{ $event->rejection_reason ?: 'Tidak dicantumkan' }}
                                @else
                                    -
                                @endif
                            </td>

                            @if($isAdminOrWaka)
                                <td class="text-end pe-3">
                                    <a href="{{ route('events.edit', $event->id) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                        Verifikasi
                                    </a>
                                </td>
                            @endif
                        </tr>

                    @empty
                        <tr>
                            <td colspan="{{ $isAdminOrWaka ? 8 : 7 }}"
                                class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada pengajuan peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

@endsection
